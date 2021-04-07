<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserPostsModel extends Model {

    protected $table = "user_posts";

    protected $fillable = [
        'user_id',
        'description',
        'image'
    ];

    public static function getPostsByUsername($username){
        $user = User::where('username', $username)->first();
        if($user){
            $posts = static::where('user_id', $user->id)->get();
            return $posts;
        }else{
            return [];
        }

    }
}
