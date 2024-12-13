<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{

    public function register(Request $request)
    {
        try {

            // validation
            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            // check email already exist
            if (User::where('email', $request->email)->first()) {
                return $this->sendError("Email already exists", 409);
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $request->password;

            if ($request->hasFile('avatar')) {
                $avatar_url = $this->upload('avatar', 'avatar');
                $user->avatar = $avatar_url;
            }

            $user->save();

            $token = $user->createToken('user-token')->plainTextToken;

            return $this->sendSuccess(['user' => $user, 'token' => $token], "User Added Successfully");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function login(Request $request)
    {
        try {

            // validation
            $validation = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                $token = $user->createToken('user-token')->plainTextToken;
                return $this->sendSuccess(['user' => $user, 'token' => $token], "Login Successfully");
            }

            // wrong credentials
            return $this->sendError("Invalid email and password", 401);

        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function logout()
    {
        try {
            auth()->user()->currentAccessToken()->delete();
            return $this->sendSuccess([], "Logout Successfully", 200);
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

}
