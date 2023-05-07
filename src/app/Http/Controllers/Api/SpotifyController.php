<?php

namespace App\Http\Controllers\Api;

use App\Services\SpotifyService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Laravel\Socialite\Facades\Socialite;

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
        return Socialite::driver('spotify')->redirect();
    }

    public function spotifyLoginCallback()
    {
        dd(Socialite::driver('spotify')->user());
    }
}
