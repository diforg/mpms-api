<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('artist', 255)->nullable();
            $table->string('album', 255)->nullable();
            $table->year('year')->nullable();
            $table->integer('track_number')->nullable();
            $table->string('gender', 255)->nullable();
            $table->time('length')->nullable();
            $table->boolean('private_song', 1)->default(0);
            $table->string('path', 255);
            $table->string('system_name', 255)->unique();
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
        Schema::dropIfExists('songs');
    }
}
