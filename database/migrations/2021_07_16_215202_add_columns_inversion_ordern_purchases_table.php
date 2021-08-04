<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInversionOrdernPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orden_purchases', function (Blueprint $table) {
            $table->bigInteger('inversion_id')->unsigned()->nullable();
            $table->foreign('inversion_id')->references('id')->on('inversions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orden_purchases', function (Blueprint $table) {
            $table->dropForeign(['inversion_id']);
        });
    }
}
