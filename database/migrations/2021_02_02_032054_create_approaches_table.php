<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApproachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approaches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image')->nullable();
            $table->string('title');
            $table->string('filename')->nullable();
            $table->string('location');
            $table->string('thumbnail')->nullable();
            $table->string('format')->default('mp4');
            $table->string('duration')->nullable();
            $table->string('filesize')->nullable();
            $table->string('bitrate')->nullable();
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
        Schema::dropIfExists('approaches');
    }
}
