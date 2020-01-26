<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMenuoptioncategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menuoptioncategory', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->integer('order');
            $table->string('icon', 60)->default('glyphicon glyphicon-expand');
            $table->integer('menuoptioncategory_id')->unsigned()->nullable();
            $table->string('position', 1)->nullable(); // V = vertical , H = Horizontal
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
        Schema::dropIfExists('menuoptioncategory');
    }
}
