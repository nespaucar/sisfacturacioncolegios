<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMenuoption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menuoption', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->string('link', 120);
            $table->integer('order');
            $table->string('icon', 60)->default('glyphicon glyphicon-expand');
            $table->integer('menuoptioncategory_id')->unsigned();
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
        Schema::dropIfExists('menuoption');
    }
}
