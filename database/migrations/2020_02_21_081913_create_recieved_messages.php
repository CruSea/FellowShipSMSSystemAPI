<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecievedMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recieved_messages', function (Blueprint $table) {
            $table->bigIncrements('message_id');
            $table->string('message');
            $table->string('sent_from')->unique();
            $table->string('sender_name');
            $table->string('received_date');
            $table->bigInteger('fellowship_id')->unsigned()->nullable();
            $table->foreign('fellowship_id')->references('fellow_id')->on('fellowships')->onDelete('cascade');
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
        Schema::dropIfExists('recieved_messages');
    }
}
