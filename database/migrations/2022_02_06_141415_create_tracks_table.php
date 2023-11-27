<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('song_id');
            $table->unsignedBigInteger('playlist_id');
            $table->string('name', 255);
            $table->integer('track_number')->nullable();
            $table->text('description')->nullable();
            $table->year('year')->nullable();
            $table->char('sensibility', 2)->nullable();
            $table->text('tag')->nullable(); //tagged feelings or specific words to describe this track
            $table->timestamps();

            $table->foreign('song_id')->references('id')->on('songs');
            $table->foreign('playlist_id')->references('id')->on('playlists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}
