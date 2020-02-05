<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       /* Schema::create('sent_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message');
            $table->string('sent_to');
            $table->boolean('is_sent');
            $table->boolean('is_delivered');
            $table->integer('sms_port_id')->unsigned()->nullable();
            $table->foreign('sms_port_id')->references('id')->on('sms_port')->onDelete('cascade');
            $table->boolean('is_removed')->default(false);
            $table->json('sent_by');
            $table->timestamps();
       });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('send_message');
    }
}
