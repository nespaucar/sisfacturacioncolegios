<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local', function (Blueprint $table) {
            $table->increments('id');
            $table->string("serie", 10);
            $table->integer('serie2')->unsigned()->nullable();
            $table->integer('serie3')->unsigned()->nullable();
            $table->string("nombre", 80);
            $table->string("descripcion", 100);
            $table->string("tipo", 1);//NACIONAL //PARTICULAR
            $table->string("logo", 80);
            $table->integer("local_id")->unsigned()->nullable();
            $table->string("estado", 1);//HABILITADO //DESHABILITADO
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
        Schema::dropIfExists('local');
    }
}
