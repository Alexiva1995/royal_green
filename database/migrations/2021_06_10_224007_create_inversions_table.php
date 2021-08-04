<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inversions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('iduser')->unsigned();;
            $table->foreign('iduser')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('package_id')->unsigned();
            $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
            //$table->bigInteger('orden_id')->unsigned();
            //$table->foreign('orden_id')->references('id')->on('orden_purchases')->onUpdate('cascade')->onDelete('cascade');
            $table->double('invertido');
            $table->double('ganacia');
            $table->double('retiro');
            $table->double('capital');
            $table->double('progreso');
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('porcentaje_fondo')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1 - activo , 2 - culminada');
            $table->tinyInteger('status_por_pagar')->default(1)->comment('1 - por Pagar , 0 - Pagado');
            $table->double('ganancia_acumulada')->default(0);
            $table->decimal('porcentaje_utilidad')->nullable();
            $table->double('max_ganancia')->nullable();
            $table->double('restante')->nullable();
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
        Schema::dropIfExists('inversions');
    }
}
