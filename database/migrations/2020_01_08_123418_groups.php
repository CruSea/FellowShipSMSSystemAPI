<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Groups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
        
            $table->bigIncrements('group_id');
            $table->string('group_name');
            $table->string('description');
            $table->string('created_by');
            $table->bigInteger('contacts_id')->unsigned()->nullable();
            $table->bigInteger('fellowship_id')->unsigned();
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
        //
    }
}
