<?php

namespace App\Services;

use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\Session as SpotifySession;
use Exception;
use Illuminate\Support\Facades\Log;

class SpotifyService
{
    private SpotifyWebAPI $shopifyWebApi;
    private SpotifySession $session;

    public function __construct()
    {
        $this->session = new SpotifySession(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret'),
            config('services.spotify.redirect')
        );

        $this->shopifyWebApi = new SpotifyWebAPI();
    }

    /**
     * アクセストークンをセット
     *
     * @param string $accessToken
     * @return void
     */
    public function setupAccessToken(string $accessToken): void
    {
        $this->shopifyWebApi->setAccessToken($accessToken);
    }

    /**
     * アクセストークンを更新
     *
     * @param string $refreshToken
     * @return array
     * @throws Exception
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        try {
            $this->session->setRefreshToken($refreshToken);
            $this->session->refreshAccessToken();

            // 新しいトークン情報を返却
            return [
                'access_token' => $this->session->getAccessToken(),
                'refresh_token' => $this->session->getRefreshToken(), // 新しいリフレッシュトークンが発行される場合もある
                'expires_in' => $this->session->getTokenExpiration()
            ];
        } catch (Exception $e) {
            Log::error('トークンリフレッシュエラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * アーティスト検索
     *
     * @param string $query
     * @param int $limit
     * @return array|object
     * @throws Exception
     */
    public function searchArtist(string $query, int $limit = 5): object|array
    {
        try {
            return $this->shopifyWebApi->search($query, 'artist', [
                'limit' => $limit,
                'market' => 'JP'
            ]);
        } catch (Exception $e) {
            Log::error('アーティスト検索エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * アーティストのトップトラック取得
     *
     * @param string $artistId
     * @return array|object
     * @throws Exception
     */
    public function getArtistTopTracks(string $artistId): object|array
    {
        try {
            return $this->shopifyWebApi->getArtistTopTracks($artistId, [
                'country' => 'JP'
            ]);
        } catch (Exception $e) {
            Log::error('トップトラック取得エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * プレイリスト作成
     *
     * @param string $userId
     * @param string $name
     * @return array|object
     * @throws Exception
     */
    public function createPlaylist(string $userId, string $name): object|array
    {
        try {
            return $this->shopifyWebApi->createPlaylist($userId, [
                'name' => $name,
                'public' => false,
                'description' => 'Created by DJ tamanoyu'
            ]);
        } catch (Exception $e) {
            Log::error('プレイリスト作成エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * トラック追加
     *
     * @param string $playlistId
     * @param array $trackUris
     * @return bool|string|null
     * @throws Exception
     */
    public function addTracksToPlaylist(string $playlistId, array $trackUris): bool|string|null
    {
        try {
            return $this->shopifyWebApi->addPlaylistTracks($playlistId, $trackUris);
        } catch (Exception $e) {
            Log::error('トラック追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ユーザー情報取得
     *
     * @return array|object
     * @throws Exception
     */
    public function getMe(): object|array
    {
        try {
            return $this->shopifyWebApi->me();
        } catch (Exception $e) {
            Log::error('ユーザー情報取得エラー: ' . $e->getMessage());
            throw $e;
        }
    }
}