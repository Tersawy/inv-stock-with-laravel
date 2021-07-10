<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('short_name')->unique();
            $table->integer('value')->default(1);
            $table->string('operator')->default("*");
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
        Schema::dropIfExists('main_units');
    }
}
