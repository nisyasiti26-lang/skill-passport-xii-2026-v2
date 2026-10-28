<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email|string",
            "password" => "required|string",
        ]);

        if($validator->fails())
            {
                return response()->json([
                    "message" => "validator gagal",
                    "error" => $validator->errors()
                ], 422);
            }

            $user = User::where("email", $request->email)->first();

            if(!$user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    "message" => "Email atau Password salah",
                ], 401);
            }

            $token = $user->createToken("apiToken")->plainTextToken;

            return response()->json([
                "message" => "Login Berhasil",
                "user" => $user,
                "token" => $token,
                "token_type" => "Bearer"
            ], 200);
    }

    public function logout(Request $request)
    {
       $request->user()->currentAccessToken()->delete();

        return response()->json([
            "message" => "Anda Berhasil Logout"
        ], 200);
    }
}
