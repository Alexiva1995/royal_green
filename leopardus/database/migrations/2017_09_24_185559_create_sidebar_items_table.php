<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSidebarItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sidebar_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('text');
            $table->string('icon');
            $table->string('route')->nullable();
            $table->integer('level')->default(0);
            $table->integer('parent_id')->default(0);
            $table->integer('sidebar_id');
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
        Schema::dropIfExists('sidebar_items');
    }
}
