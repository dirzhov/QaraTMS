<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ApiAuthController extends Controller
{
    public function login_get() {
        return ApiResponseClass::sendResponse([], "", 401, false);
    }

    public function login_post(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        $credentials = $validator->validate();

//        $credentials = $request->validate([
//            'email' => ['required', 'email'],
//            'password' => ['required']
//            ]);

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('token')->plainTextToken;
            return ApiResponseClass::sendResponse(['user' => $user, 'token' => $token], "", 200);
        }
        return ApiResponseClass::sendResponse([], "", 400, false);
    }


    public function register(Request $request) {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'status' => ['required',Rule::in(UserStatus::values())],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status']
//            'status' => UserStatus::ACTIVE
        ]);
        $token = $user->createToken('token')->plainTextToken;

        return ApiResponseClass::sendResponse(['user' => $user, 'token' => $token], "", 201);
    }

}
