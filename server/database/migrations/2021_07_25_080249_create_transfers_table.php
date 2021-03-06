<?php

use App\Helpers\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->default("TR_1110");

            $table->float('tax', 10, 0)->default(0);
            $table->float('discount', 10, 0)->default(0);
            $table->float('shipping', 10, 0)->default(0);
            $table->tinyInteger("discount_method")->default(Constants::DISCOUNT_FIXED); // 0 Fixed, 1 Percent
            $table->float('total_price', 10, 0)->default(0);

            $table->float('items_count', 10, 0);

            $table->text('note')->nullable();
            $table->date('date')->default(date('Y-m-d'));
            $table->tinyInteger('status')->default(Constants::TRANSFER_COMPLETED);

            $table->unsignedBigInteger('from_warehouse_id');
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('restrict');

            $table->unsignedBigInteger('to_warehouse_id');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->onDelete('restrict');

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
        Schema::dropIfExists('transfers');
    }
}
