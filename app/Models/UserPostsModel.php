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
            return $user->posts()->with('attachments')->paginate();
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
}
