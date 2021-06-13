<?php

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            $table->integer('tax')->default(0);
            $table->integer('discount')->default(0);
            $table->tinyInteger("discount_method")->default(Product::DISCOUNT_FIXED); // 0 Fixed, 1 Percent
            $table->tinyInteger('status')->default(Purchase::RECEIVED);
            $table->integer('shipping')->default(0);

            $table->text('note')->nullable();
            $table->date('date')->default(date('Y-m-d'));

            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');

            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('restrict');

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
        Schema::dropIfExists('purchases');
    }
}