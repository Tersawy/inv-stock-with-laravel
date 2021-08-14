<?php

use App\Helpers\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();

            $table->float("price", 10, 0);
            $table->float("quantity");
            $table->float('tax', 10, 0)->default(0);
            $table->tinyInteger("tax_method")->default(Constants::TAX_EXCLUSIVE); // 0 Exclusive, 1 Inclusive
            $table->float('discount', 10, 0)->default(0);
            $table->tinyInteger("discount_method")->default(Constants::DISCOUNT_FIXED); // 0 Fixed, 1 Percent

            $table->bigInteger('variant_id')->nullable();

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');

            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');

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
        Schema::dropIfExists('sale_details');
    }
}
