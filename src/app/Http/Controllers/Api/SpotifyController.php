<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class SpotifyController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        dd('here');
    }
}
