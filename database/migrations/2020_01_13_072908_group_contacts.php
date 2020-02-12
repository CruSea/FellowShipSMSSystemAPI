<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GroupContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('group_contacts', function (Blueprint $table) {
        
            $table->bigIncrements('Id');
            $table->string('fullname');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('acadamic_department');
            $table->string('fellow_department');
            $table->string('gender');
            $table->string('graduation_year');
            $table->bigInteger('fellowship_id')->unsigned();
            $table->foreign('fellowship_id')->references('fellow_id')->on('fellowships')->onDelete('cascade');
            $table->bigInteger('contacts_id')->unsigned()->nullable();
            $table->foreign('contacts_id')->references('group_id')->on('groups')->onDelete('cascade');
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
