<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\SendGmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthenController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function createUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required|min:6'
            ]);
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => __('Validation error'),
                    'errors' => $validateUser->errors()
                ], Response::HTTP_ACCEPTED);
            }
            $user = new User();
            $user->user_name = $request->username;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->created_at = now();
            $user->updated_at = now();
            $user->save();
            return response()->json([
                'status' => true,
                'message' => __('You have successfully created an account!'),
                'errors' => null,
                'data' => $user
            ], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], Response::HTTP_ACCEPTED);
        }
    }

    public function editUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => __('Validation error'),
                    'errors' => $validateUser->errors()
                ], Response::HTTP_ACCEPTED);
            }
            $user = User::where('id', $request->id)->update([
                'user_name' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone
            ]);
            return response()->json([
                'status' => true,
                'message' => __('You have successfully edit an account!'),
                'errors' => null,
                'data' => $user
            ], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], Response::HTTP_ACCEPTED);
        }
    }

    public function deleteUser($id)
    {
        try {
            if ($id == null) {
                return response()->json([
                    'status' => false,
                    'message' => __('User Id') . __('notexist'),
                    'errors' => null,
                    'data' => null
                ], Response::HTTP_ACCEPTED);
            }
            $deleted = User::where('id', $id)->first();

            if (!$deleted) {
                return response()->json([
                    'status' => false,
                    'message' => __('Post') . __('notexist'),
                    'errors' => null,
                    'data' => null
                ], Response::HTTP_ACCEPTED);
            }
            $deleted = User::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'message' => __('Success'),
                'errors' => null,
                'data' => $deleted
            ], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListUser(Request $request)
    {
        $users = User::all();
        return response()->json([
            'status' => true,
            'message' => __('Success'),
            'errors' => null,
            'data' => $users
        ], Response::HTTP_ACCEPTED);
    }

    public function handleForgotpassword(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required',
                'email' => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => __('Validation error'),
                    'errors' => $validateUser->errors()
                ], Response::HTTP_ACCEPTED);
            }

            $user = User::where('user_name', $request->username)->orWhere('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => __('Login information is incorrect'),
                ], Response::HTTP_ACCEPTED);
            }

            $currentDateTime = new DateTime();
            $user->otp = mt_rand(100000, 999999);
            $user->expired_otp = $currentDateTime->modify('+5 minutes')->format('Y-m-d H:i:s');

            $user->save();

            if ($user->email) {
                Mail::to($user->email)->send(new SendGmail($user->otp));
            }

            return response()->json([
                'status' => true,
                'message' => __('Success'),
                'errors' => null,
                "data" => null
            ], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], Response::HTTP_ACCEPTED);
        }
    }

    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => __('Input data is incorrect'),
                    'errors' => $validateUser->errors()
                ], Response::HTTP_ACCEPTED);
            }

            $user = User::where('user_name', $request->username)->orWhere('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => __('Login information is incorrect'),
                    'errors' => __('LOGIN_FALSE'),
                ], Response::HTTP_ACCEPTED);
            } else {
                if (Hash::check($request->password, $user->password, [])) {
                    $token =  $user->createToken('token')->plainTextToken;
                    $cookie = cookie("token", $this->encryptText($token), 60 * 24);
                    $user->token = $token;
                    return response()->json([
                        'status' => true,
                        'message' => __('Login successfully'),
                        'errors' => null,
                        "data" => $user
                    ], Response::HTTP_ACCEPTED)->withCookie($cookie);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => __('Login information is incorrect'),
                        'errors' => "LOGIN_FALSE",
                    ], Response::HTTP_ACCEPTED);
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], Response::HTTP_ACCEPTED);
        }
    }

    public function logoutUser(Request $request)
    {
        try {
            if (Auth::check()) {
                $request->user()->currentAccessToken()->delete();
                $cookie = Cookie::forget('token');
                return response()->json([
                    'status' => true,
                    'message' => __('Logout successfully'),
                    'errors' => null,
                    "data" => null
                ], Response::HTTP_ACCEPTED)->withCookie($cookie);   
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], Response::HTTP_ACCEPTED);
        }
    }

    private function encryptText($text)
    {
        $method = 'aes-256-cbc';
        $ivLength = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encryptedText = openssl_encrypt($text, $method, env('APP_KEY', null), 0, $iv);
        return base64_encode($iv . $encryptedText);
    }
}
