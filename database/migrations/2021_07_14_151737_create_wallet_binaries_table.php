<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletBinariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_binaries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('iduser')->unsigned();
            $table->foreign('iduser')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('referred_id')->unsigned()->nullable();
            $table->foreign('referred_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('orden_purchase_id')->unsigned()->nullable();
            $table->foreign('orden_purchase_id')->references('id')->on('orden_purchases')->onUpdate('cascade')->onDelete('cascade');
            // $table->bigInteger('liquidation_id')->unsigned()->nullable();
            // $table->foreign('liquidation_id')->references('id')->on('liquidactions');
            $table->double('puntos_d')->default(0)->comment('puntos del lado derecho');
            $table->double('puntos_i')->default(0)->comment('puntos del lado izquierdo');
            //$table->decimal('debito')->default(0)->comment('entrada de cash');
            //$table->decimal('credito')->default(0)->comment('salida de cash');
            //$table->decimal('balance')->nullable()->comment('balance del cash');
            $table->string('side', 1);
            $table->string('descripcion');
            $table->decimal('puntos_reales')->default(0)->comment('puntos ganados sin modificar');
            $table->tinyInteger('status')->default(0)->comment('0 - En espera, 1 - Pagado (liquidado), 2 - Cancelado');
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
        Schema::dropIfExists('wallet_binaries');
    }
}
