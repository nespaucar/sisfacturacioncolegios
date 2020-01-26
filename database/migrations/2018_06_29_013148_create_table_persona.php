<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePersona extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persona', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombres',100)->nullable();
            $table->string('apellidopaterno',100)->nullable();
            $table->string('apellidomaterno',100)->nullable();
            $table->integer("local_id")->unsigned()->nullable();
            $table->char("tipo", 1)->default('A');//A: ADMINISTRADOR, S: ESTUDIANTE
            $table->char('dni',8)->nullable();
            $table->string('direccion',120)->nullable();
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
        Schema::dropIfExists('persona');
    }
}
