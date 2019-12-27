<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add-contact', function (Blueprint $table) {

                    $table->bigIncrements('id');
                    $table->string('full_name');
                    $table->string('phone_number');
                    $table->string('email')->unique();
                    $table->string('fellow_dep');
                    $table->string('acadamic_dep');
                    $table->string('graduate_year');
                    $table->rememberToken();
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
