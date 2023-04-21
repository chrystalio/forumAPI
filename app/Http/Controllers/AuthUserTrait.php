<?php

namespace App\Http\Controllers;

use PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

trait AuthUserTrait
{
    public function getAuthUser()
    {
        try {
            $user = auth()->user();
        } catch (UserNotDefinedException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized, You must login first'
            ], 403);
        }

        return $user;
    }
}
