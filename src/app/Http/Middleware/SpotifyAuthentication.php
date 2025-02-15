<?php

namespace App\Http\Middleware;

use App\Services\SpotifyService;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SpotifyAuthentication
{
    /**
     * @var SpotifyService
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
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('spotify_auth')) {
            return response()->json(['error' => '認証が必要です'], 401);
        }

        $auth = session('spotify_auth');

        // トークンの有効期限をチェック
        if (now()->gt(Carbon::parse($auth['expires_at']))) {
            try {
                // トークンリフレッシュを実行
                $newTokens = $this->spotifyService->refreshAccessToken($auth['refresh_token']);

                // セッションのトークン情報を更新
                session([
                    'spotify_auth' => [
                        'access_token' => $newTokens['access_token'],
                        'refresh_token' => $newTokens['refresh_token'] ?? $auth['refresh_token'],
                        'expires_at' => now()->addSeconds($newTokens['expires_in']),
                        'spotify_id' => $auth['spotify_id']
                    ]
                ]);

            } catch (Exception $e) {
                Log::error('トークンリフレッシュに失敗: ' . $e->getMessage());
                return response()->json(['error' => '認証の更新に失敗しました'], 401);
            }
        }

        return $next($request);
    }
}
