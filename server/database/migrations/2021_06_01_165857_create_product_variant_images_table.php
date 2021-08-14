<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variant_images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('default')->default(0);
            $table->unsignedBigInteger('variant_id');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
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
        Schema::dropIfExists('product_variant_images');
    }
}
