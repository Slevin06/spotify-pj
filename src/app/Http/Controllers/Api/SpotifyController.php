<?php

namespace App\Http\Controllers\Api;

use App\Services\SpotifyService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Laravel\Socialite\Facades\Socialite;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private SpotifyService $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    public function index()
    {
        return view('index');
    }

    public function toSpotifyLogin()
    {
        return Socialite::driver('spotify')
            ->scopes([
                'user-read-private', // ユーザーのプロフィール情報の読み取り
                'user-read-email', // ユーザーのメールアドレスの読み取り
                'user-library-read', // ユーザーのライブラリー情報の読み取り
                'playlist-modify-public', // ユーザーのプライベートプレイリストの読み取り
                'playlist-modify-private', // ユーザーのパブリックプレイリストの編集
                'user-read-private', // ユーザーのプライベートプレイリストの編集
            ])
            ->redirect();
    }

    public function spotifyLoginCallback()
    {
        // TODO: test. アーティストの曲一覧を取得したい

//        dd(Socialite::driver('spotify')->user());
        $user = Socialite::driver('spotify')->user();

        // TODO: アクセストークンをsessionに保存しフォーム画面へ
        $accessToken = $user->token;

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        // TODO: test user
        $me = $api->me();

        // TODO: 既存のプレイリスト情報一覧
//        $playlists = $api->getUserPlaylists($me->id);

        // TODO: 新規プレイリスト作成
//        $date = Carbon::now();
//        $formattedDate = $date->format('YmdHi');
//        $playlistName = 'dj-tamayu-' . $formattedDate;
//        $playlist = $api->createPlaylist(['name' => $playlistName]);

         // TODO: アーティスト名で検索
//        $artistName = 'くじら';
//        $type = 'artist';
//        $artistData = $api->search($artistName, $type, ['limit' => 1]);

        // TODO: アーティストのトップ曲リストを取得
//        $topTracks = $api->getArtistTopTracks($artistData->artists->items[0]->id, ['country' => 'JP']);
//        dd($topTracks);
    }
}
