<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimientoTable extends Migration
{
    public function up()
    {
        Schema::create('movimiento', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('serie')->unsigned()->nullable();
            $table->integer('numero')->unsigned()->nullable();
            $table->date('fecha');
            $table->integer('persona_id')->unsigned()->nullable();
            $table->integer('responsable_id')->unsigned()->nullable();
            $table->integer('conceptopago_id')->unsigned()->nullable();
            $table->integer('tipomovimiento_id')->unsigned()->nullable();
            $table->integer('tipodocumento_id')->unsigned()->nullable();
            $table->decimal('totalefectivo', 10, 2)->nullable();
            $table->decimal('totalvisa', 10, 2)->nullable();
            $table->decimal('totalmaster', 10, 2)->nullable();
            $table->decimal('igv', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->string('comentario', 500)->nullable();
            $table->string('voucher', 20)->nullable();
            $table->decimal('totalpagado', 10, 2)->nullable();
            $table->integer('movimiento_id')->unsigned()->nullable();
            $table->char('estado', 1)->default("P"); //P: PAGADO; a: ANULADO
            $table->string('ruc', 11)->nullable();
            $table->string('razon', 80)->nullable();
            $table->string('direccion', 80)->nullable();
            $table->integer('cuota_id')->unsigned()->nullable();
            $table->integer('local_id')->unsigned()->nullable();
            $table->integer('cicloacademico_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimiento');
    }
}
