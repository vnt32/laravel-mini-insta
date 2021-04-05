<?php

namespace App\Http\Controllers\Api\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $creds = $request->only(['email', 'password']);
        if(!$token = auth()->attempt($creds)){
            return response()->json(['error'=> true], 401);
        }
        return response()->json(['token' => $token]);
    }

    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ];
        $reg = $request->only(['name', 'username', 'email', 'password']);
        $validator = Validator::make($reg, $rules);
        if($validator->fails()){
            return response()->json(['error'=> true, 'message' => $validator->errors()], 401);
        }
        $name = $request->name;
        $username = $request->username;
        $email = $request->email;
        $password = $request->password;
        $user = User::create(['name' => $name, 'username' => $username, 'email' => $email, 'password' => Hash::make($password)]);
        $token = Auth::login($user);
        return response()->json(['token' => $token]);
    }

    public function refresh() {
        try{
            $token = auth()->refresh();
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()], 401);
        }
        return response()->json(['token' => $token]);
    }

    public function getMe() {
        try{
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()], 401);
        }
        return response()->json($user);
    }

    public function editMe(Request $req) {
        try{
            $user = Auth::user();

            $rules = [];

            if($req->name){
                $rules['name'] = ['string', 'max:255'];
            }
            if($req->username != $user->username) {
                $rules['username'] = ['unique:users'];
            }
            if($req->email != $user->email) {
                $rules['email'] = ['unique:users'];
            }
            if($req->avatar) {
                $rules['avatar'] = ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:5144'];
            }


            $validator = Validator::make($req->all(), $rules);
            if($validator->fails()){
                return response()->json(['error' => true, 'message' => $validator->errors()], 400);
            }

            if($req->name) $user->name = $req->name;
            if($req->email) $user->email = $req->email;
            if($req->username) $user->username = $req->username;
            if($req->avatar) {
                $avatarName = $user->id.'_avatar'.time().'.'.request()->avatar->getClientOriginalExtension();

                $user->avatar = $req->avatar->storeAs('avatars',$avatarName);
            }

            if($req->password) {
                $pass = $req->password;
                $user->password = Hash::make($pass);
            }


            $user->save();

            return response()->json(['user' => $user]);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e){
            return response()->json(['error' => true, 'message' => $e->getMessage()], 401);
        }
        return response()->json($user);
    }
}
