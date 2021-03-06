<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserPostsModel extends Model {

    protected $table = "user_posts";

    protected $fillable = [
        'user_id',
        'description',
        'thumb'
    ];

    public static function getPostsByUsername($username){
        $user = User::where('username', $username)->first();
        if($user){
            return $user->posts()->withCount('likes')->with('attachments')->paginate();
        }else{
            return ['data' => [], 'last_page' => 1];
        }

    }

    public function attachments()
    {
        return $this->hasMany(AttachmentsModel::class, 'post_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes(){ //or simply likes
        return $this->belongsToMany(User::class, 'likes', 'user_id', 'post_id')->withTimestamps();
    }

    public function isLiked($id){
        return !! $this->likes()->where('user_id', $id)->count();
    }
}
