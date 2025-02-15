<?php

namespace App\Http\Controllers\Api;

use App\Services\SpotifyService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SpotifyController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @var SpotifyService $spotifyService
     */
    private SpotifyService $spotifyService;

    /**
     * @param SpotifyService $spotifyService
     */
    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function index(): Factory|View|\Illuminate\Foundation\Application|Application
    {
        return view('index');
    }

    /**
     * Spotifyログイン実行
     *
     * @return mixed
     */
    public function toSpotifyLogin(): mixed
    {
        return Socialite::driver('spotify')
            ->scopes([
                'user-read-private', // ユーザーのプロフィール情報の読み取り
                'user-read-email', // ユーザーのメールアドレスの読み取り
                'user-library-read', // ユーザーのライブラリー情報の読み取り
                'user-read-private', // ユーザーのプライベートプレイリストの編集
                'playlist-modify-public', // ユーザーのプライベートプレイリストの読み取り
                'playlist-modify-private', // ユーザーのパブリックプレイリストの編集
            ])
            ->redirect();
    }

    /**
     * Spotifyログインコールバック
     *
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Redirector
     */
    public function spotifyLoginCallback(): \Illuminate\Foundation\Application|Redirector|Application|RedirectResponse
    {
        try {
            $spotifyUser = Socialite::driver('spotify')->user();

            // セッションに必要最小限の情報を保存
            // TODO: ゆくゆくDBに？
            session([
                'spotify_auth' => [
                    'access_token' => $spotifyUser->token,
                    'refresh_token' => $spotifyUser->refreshToken,
                    'expires_at' => now()->addSeconds($spotifyUser->expiresIn),
                    'spotify_id' => $spotifyUser->getId()
                ]
            ]);

            return redirect('/form');

        } catch (Exception $e) {
            Log::error('Spotify認証エラー: ' . $e->getMessage());
            return redirect('/')->with('error', 'ログインに失敗しました');
        }
    }

    /**
     * 認証チェック
     *
     * @return JsonResponse
     */
    public function checkAuth(): JsonResponse
    {
        try {
            // セッションチェック
            if (!session()->has('spotify_auth')) {
                return response()->json([
                    'authenticated' => false,
                    'message' => 'セッションが存在しません'
                ]);
            }

            $auth = session('spotify_auth');

            // トークンの有効期限チェック
            if (now()->gt(Carbon::parse($auth['expires_at']))) {
                session()->forget('spotify_auth'); // 期限切れセッションを削除
                return response()->json([
                    'authenticated' => false,
                    'message' => 'セッションの有効期限が切れました'
                ]);
            }

            // アクセストークンの存在確認
            if (empty($auth['access_token'])) {
                session()->forget('spotify_auth');
                return response()->json([
                    'authenticated' => false,
                    'message' => 'アクセストークンが見つかりません'
                ]);
            }

            // Spotifyサービスでトークンの有効性を確認
            try {
                $this->spotifyService->setupAccessToken($auth['access_token']);
                $this->spotifyService->getMe(); // トークンの有効性をチェック
            } catch (Exception $e) {
                session()->forget('spotify_auth');
                return response()->json([
                    'authenticated' => false,
                    'message' => 'トークンが無効です'
                ]);
            }

            return response()->json([
                'authenticated' => true,
                'spotify_id' => $auth['spotify_id']
            ]);

        } catch (Exception $e) {
            Log::error('認証チェックエラー: ' . $e->getMessage());
            return response()->json([
                'authenticated' => false,
                'message' => '認証チェックに失敗しました'
            ]);
        }
    }

    /**
     * アーティスト検索
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchArtists(Request $request): JsonResponse
    {
        try {
            $query = $request->input('query');

            if (empty($query)) {
                return response()->json(['error' => '検索クエリが必要です'], 400);
            }

            $this->spotifyService->setupAccessToken(session('spotify_auth.access_token'));
            $result = $this->spotifyService->searchArtist($query);

            return response()->json($result);

        } catch (Exception $e) {
            Log::error('アーティスト検索エラー: ' . $e->getMessage());
            return response()->json(['error' => '検索に失敗しました'], 500);
        }
    }

    /**
     * プレイリスト作成
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPlaylist(Request $request): JsonResponse
    {
        try {
            $this->spotifyService->setupAccessToken(session('spotify_auth.access_token'));

            // ユーザー情報の取得
            $me = $this->spotifyService->getMe();

            // プレイリスト名の生成（または受け取り）
            $playlistName = $request->input('name', 'DJ tamanoyu - ' . now()->format('YmdHi'));

            // プレイリストの作成
            $playlist = $this->spotifyService->createPlaylist($me->id, $playlistName);

            // 指定されたアーティストのトップトラックを取得して追加
            $artistIds = $request->input('artist_ids', []);
            $trackUris = [];

            foreach ($artistIds as $artistId) {
                $topTracks = $this->spotifyService->getArtistTopTracks($artistId);
                foreach ($topTracks->tracks as $track) {
                    $trackUris[] = $track->uri;
                }
            }

            if (!empty($trackUris)) {
                $this->spotifyService->addTracksToPlaylist($playlist->id, $trackUris);
            }

            return response()->json([
                'success' => true,
                'playlist' => $playlist
            ]);

        } catch (Exception $e) {
            Log::error('プレイリスト作成エラー: ' . $e->getMessage());
            return response()->json(['error' => 'プレイリストの作成に失敗しました'], 500);
        }
    }

    /**
     * ログアウト
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            // Spotifyの認証情報をセッションから削除
            session()->forget('spotify_auth');

            return response()->json([
                'success' => true,
                'message' => 'ログアウトしました'
            ]);
        } catch (Exception $e) {
            Log::error('ログアウトエラー: ' . $e->getMessage());
            return response()->json([
                'error' => 'ログアウトに失敗しました'
            ], 500);
        }
    }
}
