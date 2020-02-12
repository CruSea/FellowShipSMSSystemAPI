<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      /*  Schema::create('contact_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('group_id')->unsigned();
            $table->foreign('group_id')->references('group_id')->on('groups')->onDelete('cascade');
            $table->bigInteger('contact_id')->unsigned();
            $table->foreign('contact_id')->references('contact_id')->on('contacts')->onDelete('cascade');
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
      //  Schema::dropIfExists('contact_groups');
    }
}
