<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
     // Register User
     public function register(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'username' => 'required|unique:users',
             'email' => 'required|email|unique:users',
             'password' => 'required|min:6',
         ]);
 
         if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
         }
 
         $user = User::create([
             'username' => $request->username,
             'email' => $request->email,
             'password' => bcrypt($request->password),
             'role' => $request->role ?: 'user',
         ]);
 
         $token = JWTAuth::fromUser($user);
 
         return response()->json(compact('user', 'token'), 201);
     }
 
     // Login User
     public function login(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'email' => 'required|email',
             'password' => 'required',
         ]);
 
         if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
         }
 
         $credentials = $request->only('email', 'password');
 
         try {
             if (! $token = JWTAuth::attempt($credentials)) {
                 return response()->json(['error' => 'Unauthorized'], 401);
             }
         } catch (JWTException $e) {
             return response()->json(['error' => 'Could not create token'], 500);
         }
 
         return response()->json(compact('token'));
     }
 
     // Refresh Token
     public function refresh()
     {
         try {
             $token = JWTAuth::parseToken()->refresh();
         } catch (JWTException $e) {
             return response()->json(['error' => 'Token can not be refreshed'], 500);
         }
 
         return response()->json(compact('token'));
     }
 
     // Logout User
     public function logout(Request $request)
     {
         try {
             // Invalidate the token by adding it to the blacklist
             JWTAuth::invalidate(JWTAuth::getToken());
     
             return response()->json(['message' => 'Successfully logged out']);
         } catch (JWTException $e) {
             return response()->json(['error' => 'Could not log out, please try again'], 500);
         }
     }
 
     // Get Authenticated User
     public function me()
     {
         return response()->json(Auth::user());
     }
}
