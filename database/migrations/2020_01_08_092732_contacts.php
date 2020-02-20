<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Contacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
        
            $table->bigIncrements('contact_id');
            $table->string('full_name');
            $table->string('phone_number');
            $table->string('email')->unique()->nullable();
            $table->string('acadamic_dep')->nullable();
            $table->string('fellow_dep');
            $table->string('gender');
            $table->string('graduate_year');
            $table->integer('is_under_graduate');
            $table->integer('is_this_year_gc');
            $table->string('id_number');
            $table->longText('photo_url')->nullable();
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
