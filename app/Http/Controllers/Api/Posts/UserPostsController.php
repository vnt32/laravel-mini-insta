<?php

namespace App\Http\Controllers\Api\Posts;

use App\Models\AttachmentsModel;
use App\User;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
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

    public function addPost(Request $req)
    {
        $data = $req->all();
        $userId = Auth::id();

        $validator = Validator::make(
            $data,
            [
                'attach' => 'required',
                'attach.*' => 'mimes:jpg,jpeg,png|max:5144'
            ],[
                'attach.*.required' => 'Please upload an image',
                'attach.*.mimes' => 'Only jpeg,png images are allowed',
                'attach.*.max' => 'Sorry! Maximum allowed size for an image is 5MB',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error'=> true, 'message' => $validator->errors()], 401);
        }

        $result = DB::transaction(function() use ($userId, $data) {
            //Делаем thumb(предосмотр)
            $requestImage = $data['attach'][0];
            $requestImagePath = $requestImage->getRealPath() . '.jpg';
            $interventionImage = Image::make($requestImage)
                ->resize(400, null, function ($constraint) { $constraint->aspectRatio(); } )
                ->encode('jpg');
            $interventionImage->save($requestImagePath);
            $thumb = Storage::putFileAs('thumbs', new File($requestImagePath), $userId.time().'thumb.jpg');
            //создаем пост
            $post = UserPostsModel::create(['user_id' => $userId, 'description' => $data['description'], 'thumb' => $thumb]);

            //аттачим массив фоток
            $attachments = [];
            foreach ($data['attach'] as $key=>$file) {
                $attachName = $userId.'_'.$post->id.'_'.$key.'_post_image'.time().'.'.$file->getClientOriginalExtension();
                $attachSystem = $file->storeAs('post_images',$attachName);
                $attach = AttachmentsModel::create(['name' => $attachName, 'path' => $attachSystem, 'post_id'=> $post->id]);
                array_push($attachments, $attach);
            }

            //TODO: Добавить замбы
            return ['post'=>$post, 'attachments'=>$attachments];
        });


        return response()->json(['success' => true, 'post' => $result['post']], 201);

//        $imageName = $userId.'_post_image'.time().'.'.request()->image->getClientOriginalExtension();
//        $image = $req->image->storeAs('post_images',$imageName);
//
//        if($req->description) $description = $req->description;
//        $postAfter = ['user_id' => $userId, 'description' => $description, 'image' => $image];
//        $post = UserPostsModel::create($postAfter);
//        return response()->json(['success' => true, 'post' => $post], 201);
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
        return response()->json($post::with('attachments', 'user')->get(),200);
    }

    public function index(Request $request){
        $requestImage = request()->file('image');
        $requestImagePath = $requestImage->getRealPath() . '.jpg';
        $interventionImage = Image::make($requestImage)
            ->resize(400, null, function ($constraint) { $constraint->aspectRatio(); } )
            ->encode('jpg');
        $interventionImage->save($requestImagePath);
        $url = Storage::putFileAs('thumbs', new File($requestImagePath), 'thumbnail.jpg');
        return response()->json(['url' => $url]);
    }

}
