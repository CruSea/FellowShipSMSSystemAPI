<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       /* Schema::create('group_message', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message');
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('group_id')->on('groups')->onDelete('cascade');
            $table->boolean('under_graduate');
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
        Schema::dropIfExists('group_message');
    }
}
