<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfiguracionpagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracionpago', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion', 100);
            $table->double('frecuencia', 8, 2);
            $table->string('unidad', 20);
            $table->integer('local_id')->unsigned()->nullable();
            $table->integer('alumno_id')->unsigned()->nullable();
            $table->integer('nivel_id')->unsigned()->nullable();
            $table->integer('grado_id')->unsigned()->nullable();
            $table->integer('seccion_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuracionpago');
    }
}
