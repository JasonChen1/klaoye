<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->integer('num')->default(0);
            $table->decimal('price',8,2);
            $table->decimal('subtotal',8,2);
            $table->decimal('total',8,2);
            $table->decimal('discount',8,2)->nullable()->default(0);
            $table->decimal('delivery',8,2)->nullable()->default(0);
            $table->decimal('discounted',8,2)->nullable()->default(0);
            $table->decimal('discount_total',8,2)->nullable()->default(0);
            $table->string('detail_id')->nullable();
            $table->string('color_code')->nullable();
            $table->bigInteger('product_id')->unsignedBigInteger()->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade');
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
        Schema::dropIfExists('order_details');
    }
}