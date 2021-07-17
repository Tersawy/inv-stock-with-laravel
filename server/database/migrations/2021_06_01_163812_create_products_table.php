<?php

use App\Models\Product;
use App\Helpers\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();

            $table->string("barcode_type");
            $table->string("code")->unique();

            $table->integer("price");
            $table->integer("cost");
            $table->integer("instock")->default(0);
            $table->integer("minimum")->default(0);
            $table->integer("tax")->default(0);
            $table->integer("tax_method")->default(Constants::TAX_EXCLUSIVE); // 0 Exclusive, 1 Inclusive
            $table->text("note")->nullable();

            $table->boolean("has_variants")->default(0);
            $table->boolean("has_images")->default(0);

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');

            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('restrict');

            $table->unsignedBigInteger('main_unit_id');
            $table->foreign('main_unit_id')->references('id')->on('main_units')->onDelete('restrict');

            $table->unsignedBigInteger('purchase_unit_id');
            $table->foreign('purchase_unit_id')->references('id')->on('sub_units')->onDelete('restrict');

            $table->unsignedBigInteger('sale_unit_id');
            $table->foreign('sale_unit_id')->references('id')->on('sub_units')->onDelete('restrict');

            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
}
