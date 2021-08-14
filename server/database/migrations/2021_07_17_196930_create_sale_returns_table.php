<?php

use App\Helpers\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->default("RT_1110");

            $table->float('tax', 10, 0)->default(0);
            $table->float('discount', 10, 0)->default(0);
            $table->tinyInteger("discount_method")->default(Constants::DISCOUNT_FIXED); // 0 Fixed, 1 Percent
            $table->tinyInteger('status')->default(Constants::SALE_RETURN_RECEIVED);
            $table->tinyInteger('payment_status')->default(Constants::PAYMENT_STATUS_UNPAID);
            $table->float('shipping', 10, 0)->default(0);
            $table->float('total_price', 10, 0)->default(0);
            $table->float('paid', 10, 0)->default(0);

            $table->text('note')->nullable();
            $table->date('date')->default(date('Y-m-d'));

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');

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
        Schema::dropIfExists('sale_returns');
    }
}
