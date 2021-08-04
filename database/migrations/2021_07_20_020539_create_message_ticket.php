<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_ticket', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('id_user');
            $table->bigInteger('id_admin');
            $table->bigInteger('id_ticket');
            $table->boolean('type', [0, 1])->nullable()->comment('0 - User, 1 - Admin');
            $table->longtext('message')->nullable();
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
        Schema::dropIfExists('message_ticket');
    }
}
