<?php

namespace App\Http\Controllers\Api\Posts;

use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Models\UserPostsModel;
use Illuminate\Support\Facades\Auth;

class UserPostsController extends Controller
{
    public function getUserPosts($username): \Illuminate\Http\JsonResponse
    {
        return response()->json(UserPostsModel::getPostsByUsername($username));
    }

    public function addPost(Request $req): \Illuminate\Http\JsonResponse
    {
        $userId = Auth::id();
        $description = null;
        $image = null;

        $rules = [
            'image' => ['required','image', 'mimes:jpeg,png,jpg', 'max:5144']
        ];

        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return response()->json(['error'=> true, 'message' => $validator->errors()], 401);
        }
        $imageName = $userId.'_post_image'.time().'.'.request()->image->getClientOriginalExtension();
        $image = $req->image->storeAs('post_images',$imageName);

        if($req->description) $description = $req->description;
        $postAfter = ['user_id' => $userId, 'description' => $description, 'image' => $image];
        $post = UserPostsModel::create($postAfter);
        return response()->json(['success' => true, 'post' => $post], 201);
    }

    public function removePost($id){
        $post = UserPostsModel::find($id);
        $authUserId = Auth::id();
        if(is_null($post)){
            return response()->json(['error' => true, 'message' => 'Not found!'],404);
        }
        if($post->user_id != $authUserId) {
            return response()->json(['error' => true, 'message' => 'Not permission'], 401);
        }
        $post->delete();
        return response()->json('', 200);
    }

    public function getPostById($id): \Illuminate\Http\JsonResponse
    {
        $post = UserPostsModel::find($id);
        if(is_null($post)){
            return response()->json(['error' => true, 'message' => 'Not found!'],404);
        }
        $post['user'] = User::find($post->user_id);

        return response()->json($post,200);
    }
}
