<?php

namespace App\Http\Controllers;

use App\ApiResponses;

class AuthController extends Controller
{
    use ApiResponses;

    public function login() {
        return response()->json([
            'message' => 'Hello, Login'
        ], 200);
    }
}
