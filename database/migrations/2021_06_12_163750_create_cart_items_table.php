<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->nullable()->constrained('carts')->onDelete('cascade');
            $table->integer('num')->default(0);
            $table->decimal('price',8,2);
            $table->decimal('subtotal',8,2);
            $table->decimal('total',8,2);
            $table->decimal('discount',8,2)->nullable()->default(0);
            $table->decimal('discounted',8,2)->nullable()->default(0);
            $table->decimal('discount_total',8,2)->nullable()->default(0);
            $table->string('detail_id')->nullable();
            $table->string('color_code')->nullable();
            $table->bigInteger('product_id')->unsignedBigInteger()->nullable();

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
        Schema::dropIfExists('cart_items');
    }
}
