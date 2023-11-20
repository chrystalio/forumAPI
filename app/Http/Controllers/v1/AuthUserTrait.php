<?php

namespace App\Http\Controllers\v1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

trait AuthUserTrait
{
    public function getAuthUser()
    {
        try {
            return Auth::guard('api')->userOrFail();
        } catch (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized, You must login first',
            ], 403);
        }
    }

    private function checkOwnership($owner): void
    {
        $user = $this->getAuthUser();

        if ($user->id !== $owner) {
            throw new \Exception('You are not authorized to update this post');
        }
    }
}
