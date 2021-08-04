 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('title');
            $table->longtext('description');
            $table->longtext('keyword');
            $table->enum('status', [0, 1])->default(1)->comment('0 - Mantenimiento, 1 - Produccion');
            $table->longtext('term');
            // $table->longtext('politic');
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
        Schema::dropIfExists('config');
    }
} 
