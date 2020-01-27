<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RegisterAdmins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       /* Schema::create('register_admins', function (Blueprint $table) {
        
            $table->bigIncrements('admin_id');
            $table->string('admin_name');
            $table->string('university');
            $table->string('campus');
            $table->string('email')->unique()->nullable();
            $table->string('sex');
            $table->integer('phone_number');
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
