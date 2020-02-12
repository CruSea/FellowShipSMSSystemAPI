<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     /*   Schema::create('schedule_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('sent_time');
            $table->string('message');
            $table->bigInteger('group_id')->unsigned();
            $table->foreign('group_id')->references('group_id')->on('groups')->onDelete('cascade');
            $table->bigInteger('fellowship_id')->unsigned();
            $table->foreign('fellowship_id')->references('fellow_id')->on('fellowships')->onDelete('cascade');
          //  $table->integer('event_id')->unsigned();
          //  $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->bigInteger('sms_port_id')->unsigned();
            $table->foreign('sms_port_id')->references('id')->on('sms_ports')->onDelete('cascade');
            $table->string('phone');
            $table->string('sent_to');
            $table->bigInteger('get_fellowship_id')->unsigned();
            $table->foreign('get_fellowship_id')->references('id')->on('fellowships')->onDelete('cascade');
            $table->boolean('for_under_graduate')->default(true);
            $table->string('sent_by');
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
        Schema::dropIfExists('schedule_messages');
    }
}
