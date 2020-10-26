<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')
            		->default('AIO System');
            		
            $table->string('slogan')
            		->default('Uno para todo.');

            $table->string('name_styled')
            		->default('AIO <strong>System</strong>')
            		->nullable();

            $table->string('company_name')->nullable();
            $table->string('company_email')->nullable();
            $table->string('site_email')->nullable();
            $table->text('description')->nullable();
            $table->integer('category_type')->nullable();
            $table->boolean('enable_register')->default(true)->nullable();
            $table->boolean('enable_auth_fb')->default(false)->nullable();
            $table->boolean('enable_auth_tw')->default(false)->nullable();
            $table->boolean('enable_auth_google')->default(false)->nullable();
            $table->string('version')->default('0.2.0')->nullable();
            $table->integer('keycode')->nullable();
            $table->integer('logo')->default(1)->nullable();
            $table->integer('rol_default')->default(3)->nullable();
            $table->boolean('status_web')->default(true)->nullable();
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
        Schema::dropIfExists('settings');
    }
}
