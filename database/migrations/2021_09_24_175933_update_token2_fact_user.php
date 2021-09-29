<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateToken2FactUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('token_google')->commet('guarda el token de la doble autenticacion')->nullable();
            $table->tinyInteger('activar_2fact')->default(0)->comment('activa o desactiva la doble autenticacion de un usuario, 2 - esta acitvo pero no pide');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['token_google', 'activar_2fact']);
        });
    }
}
