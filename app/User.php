<?php

namespace App;

use App\Models\UserPostsModel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'avatar', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function getByUsername($username){
        $user = static::where('username', $username)->first();
        if(!is_null($user)) {
            $user->followers_count = $user->followers()->count();
            $user->followed_count = $user->followed()->count();
            $user->posts_count = $user->posts()->count();
            if($user->id != Auth::id()) $user->followed = $user->isFollowed(Auth::id());
        }
        return $user;
    }

    public function followers()
    {
        return $this->belongsToMany(
            self::class,
            'follows',
            'followee_id',
            'follower_id'
        );
    }
    public function followed()
    {
        return $this->belongsToMany(
            self::class,
            'follows',
            'follower_id',
            'followee_id'
        );
    }

    public function getFollowers(){
        return self::followers()->get();
    }

    public function getFollowed(){
        return self::followed()->get();
    }

    public function isFollowed($id){
        return !! $this->followed()->where('followee_id', $id)->count();
    }


    public function posts()
    {
        return $this->hasMany(UserPostsModel::class);
    }

}
