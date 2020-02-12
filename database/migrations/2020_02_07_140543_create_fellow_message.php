<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFellowMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        Schema::create('fellow_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('message');
            $table->bigInteger('fellowship_id')->unsigned()->nullable();
            $table->foreign('fellowship_id')->references('fellow_id')->on('fellowships')->onDelete('cascade');
            $table->boolean('under_graduate');
            $table->boolean('is_removed')->default(false);
            $table->string('sent_by');
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
        Schema::dropIfExists('fellow_message');
    }
}
