<?php

namespace App\Http\Controllers\Api\User;

use App\Models\UserPostsModel;
use App\User;
use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller {

    public function getUserByUsername($username){
        $user = User::getByUsername($username);
        if(is_null($user)) return response()->json(['error'=> true, 'message' => 'not found'], 404);

        return response()->json($user, 201);
    }

    public function followers($username = null){
        $user = User::getByUsername($username);
        if(!$username) $user = Auth::user();

        return response()->json($user->followers()->paginate(), 201);
        //return response()->json([], 200);
    }

    public function followed($username = null){
        $user = User::getByUsername($username);
        if(!$username) $user = Auth::user();

        $users = $user->followed()->paginate();
        $updatedItems = $users->getCollection()->transform(function($item) use ($user){
            if($user->id == Auth::id()) $item->followed = Auth::User()->isFollowed($item->id);
            return $item;
        });
        $users->setCollection($updatedItems);

        return response()->json($users, 201);
        //return response()->json([], 200);
    }

    public function follow($id){
        $me = Auth::id();
        $user = User::find($id);

        if(!$user) {
            return response()->json(['error' => true, 'message' => 'User not exists!'], 401);
        }

        //Follow action
        if(!$user->isFollowed($me)){
            $user->followers()->sync([$me],false);
            return response()->json(['success'=>true]);
        }
        return response()->json(['error' => true, 'message' => 'You are followed!'], 401);
//
    }

    public function unfollow($id){
        $me = Auth::id();
        $user = User::find($id);

        if(!$user) {
            return response()->json(['error' => true, 'message' => 'User not exists!'], 401);
        }

        //unFollow action
        if($user->isFollowed($me)) {
            $user->followers()->detach([$me]);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => true, 'message' => 'You are not followed!'], 401);
    }

    public function index(){

    }

}
