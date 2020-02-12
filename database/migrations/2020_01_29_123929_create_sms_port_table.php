<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsPortTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * @return void
     */
    public function up()
    {
       /* Schema::create('sms_ports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('port_name');
            $table->string('port_type');
            $table->string('api_key');
            $table->integer('negarit_sms_port_id');
            $table->integer('negarit_campaign_id');
            $table->string('created_by');
            $table->timestamps();
        }); */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_port');
    }
}
