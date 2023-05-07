<?php

use App\Http\Controllers\Api\SpotifyController;
use App\Http\Controllers\FormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});


// TODO: アクセス時にSpotify認証ボタン画面を表示
Route::get('/', [SpotifyController::class, 'index']);

Route::get('/to-spotify-login', [SpotifyController::class, 'toSpotifyLogin']);

Route::get('/login/spotify/callback', [SpotifyController::class, 'spotifyLoginCallback']);

// TODO: プレイリスト作成フォーム。あとで使うかも
//Route::get('/', [FormController::class, 'index']);

