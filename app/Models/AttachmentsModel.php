<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttachmentsModel extends Model
{
    protected $table = "posts_attachmets";

    protected $fillable = [
        'post_id',
        'name',
        'path'
    ];

}
