<?php

namespace App\Http\Controllers\Api\User;

use App\User;
use App\Http\Controllers\Api\Controller;

class UserController extends Controller {

    public function getUserByUsername($username){
        $user = User::where('username', $username)->first();
        if(is_null($user)) return response()->json(['error'=> true, 'message' => 'not found'], 404);

        return response()->json($user, 201);
    }
}
