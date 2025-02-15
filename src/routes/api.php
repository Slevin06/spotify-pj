<?php

use App\Http\Controllers\Api\SpotifyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 認証状態チェック - ミドルウェア不要
Route::get('/spotify/check-auth', [SpotifyController::class, 'checkAuth']);

// 未ログインユーザー向けルート
Route::get('/to-spotify-login', [SpotifyController::class, 'toSpotifyLogin']);
Route::get('/login/spotify/callback', [SpotifyController::class, 'spotifyLoginCallback']);

// ログイン済みユーザー向けルート
Route::prefix('spotify')->middleware('auth.spotify')->group(function () {
    Route::get('/search-artists', [SpotifyController::class, 'searchArtists']);
    Route::post('/create-playlist', [SpotifyController::class, 'createPlaylist']);
    Route::post('/logout', [SpotifyController::class, 'logout']);
});