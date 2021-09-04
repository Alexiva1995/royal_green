<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('iduser')->unsigned();
            $table->foreign('iduser')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('referred_id')->unsigned()->nullable();
            $table->foreign('referred_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('orden_purchases_id')->unsigned()->nullable();
            // $table->foreign('orden_purchases_id')->references('id')->on('orden_purchases');
            $table->bigInteger('liquidation_id')->unsigned()->nullable();
            $table->foreign('liquidation_id')->references('id')->on('liquidactions')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('monto')->default(0)->comment('entrada de cash');
            $table->string('descripcion');
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
        Schema::dropIfExists('log_wallets');
    }
}
