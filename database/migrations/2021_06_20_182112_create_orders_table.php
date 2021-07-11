<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('no');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('paid')->default(false);
            $table->string('payment_type')->nullable();
            $table->integer('status')->default(0)->comment('0 - default, 1 - 待发货, 2 - 已发货, 3 - 已签收, 4 - 退款');
            $table->decimal('subtotal',8,2)->nulllable()->default(0);
            $table->decimal('discount_total',8,2)->nullable()->default(0);
            $table->decimal('delivery_total',8,2)->nullable()->default(0);
            $table->decimal('total',8,2)->nulllable()->default(0);
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
        Schema::dropIfExists('orders');
    }
}
