<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlumnoSeccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumno_seccion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('alumno_id')->unsigned()->nullable();
            $table->integer('cicloacademico_id')->unsigned()->nullable();
            $table->integer('seccion_id');
            $table->string('observacion', 100);
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
        Schema::dropIfExists('alumno_seccion');
    }
}
