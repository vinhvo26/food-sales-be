<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        if ($user->status == 3) {
            $request->user()->tokens()->delete();
            $cookie = Cookie::forget('token');
            return response()->json([
                'status' => false,
                'message' => __('Account has been locked'),
                'errors' => [
                    'type' => "ACCOUNT_BLOCK",
                    'status' => $user->status
                ],
                'data' => null
            ], Response::HTTP_UNAUTHORIZED)->withCookie($cookie);
        }
        return response()->json([
            'status' => true,
            'message' => __('Welcome to my website!'),
            'errors' => null,
            'data' => Auth::user()
        ], Response::HTTP_ACCEPTED);
    }
}
