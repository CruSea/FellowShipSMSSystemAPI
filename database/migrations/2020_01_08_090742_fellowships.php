<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Fellowships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      /*  Schema::create('fellowships', function (Blueprint $table) {
        
            $table->bigIncrements('fellow_id');
            $table->string('university_name');
            $table->string('university_city');
            $table->string('campus');
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
        //
    }
}
