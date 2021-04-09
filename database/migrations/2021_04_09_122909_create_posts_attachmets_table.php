<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsAttachmetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts_attachmets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('post_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('user_posts')->onDelete('cascade');
            $table->string('name');
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts_attachmets');
    }
}
