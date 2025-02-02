<?php

namespace App\Http\Controllers;

use App\Jobs\SendResetPasswordMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

    public function changePassword(Request $request)
    {
        try {
            // validation
            $validation = Validator::make($request->all(), [
                'password' => 'required|confirmed',
            ]);

            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            // change password
            $user = auth()->user();
            $user->password = Hash::make($request->password);
            $user->save();

            return $this->sendSuccess([], "Password Changed Successfully");
        } catch (\Throwable $th) {
            return $this->sendError("Internal Server Error", 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            // validation
            $validation = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->sendError("User Not Found", 404);
            }

            // create token
            $token = Str::random(60);
            $data['link'] = FacadesRequest::root() . "/reset-password/" . $token;
            $data['name'] = $user->name;
            $data['email'] = $request->email;

            // store token
            $resetToken = new PasswordResetToken();
            $resetToken->email = $data['email'];
            $resetToken->token = $token;
            $resetToken->created_at = Carbon::now();
            $resetToken->save();

            // send mail
            SendResetPasswordMail::dispatch($data['email'], $data['name'], $data['link']);

            return $this->sendSuccess([], "Password Reset Email Sent... Please Check Your Email");
        } catch (\Throwable $th) {
            return $th->getMessage();
            return $this->sendError("Internal Server Error", 500);
        }
    }

    public function resetPassword(Request $request, $token)
    {
        // validation
        $request->validate([
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);
        
        try {

            $resetPassword = PasswordResetToken::where('token', $token)->first();

            if (!$resetPassword) {
                return view('reset-password', [
                    'message' => 'Token is Invalid or Expired', 
                    'text' => 'The token you provided is either invalid or has expired. Please try again',
                    'status' => false
                ]);
                // return $this->sendError("Token is Invalid or Expired", 498);
            }

            // change password
            $user = User::where('email', $resetPassword->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // delete token
            PasswordResetToken::where('email', $resetPassword->email)->delete();

            // return $this->sendSuccess([], "Password Changed Successfully");
            return view('reset-password', [
                'message' => 'Password Changed Successfully',
                'text' => 'Please open the application and try logging in using the new password.',
                'status' => true
            ]);
        } catch (\Throwable $th) {
            // return $this->sendError("Internal Server Error", 500);
            return view('reset-password', [
                'message' => 'Internal Server Error',
                'text' => 'An internal server error was detected. Please try again.',
                'status' => false
            ]);
        }
    }
}
