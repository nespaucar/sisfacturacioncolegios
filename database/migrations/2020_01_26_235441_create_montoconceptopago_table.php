<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMontoconceptopagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('montoconceptopago', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conceptopago_id')->unsigned();
            $table->integer('local_id')->unsigned();
            $table->decimal('monto', 10, 2)->nullable();
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
        Schema::dropIfExists('montoconceptopago');
    }
}
