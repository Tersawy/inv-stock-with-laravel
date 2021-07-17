<?php

use App\Helpers\Constants;
use App\Models\PurchasePayment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseReturnPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_return_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('amount');
            $table->bigInteger('payment_method')->default(Constants::PAYMENT_CASH);
            $table->text('note')->nullable();

            $table->unsignedBigInteger('purchase_return_id');
            $table->foreign('purchase_return_id')->references('id')->on('purchase_returns')->onDelete('cascade');
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
        Schema::dropIfExists('purchase_return_payments');
    }
}
