<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('code',20)->unique();
            $table->string('name');
            $table->decimal('price',8,2);
            $table->decimal('discount',8,2)->nullable()->default(0);
            $table->decimal('delivery',8,2)->nullable()->default(0);
            $table->integer('status')->default(1)->comment('0:not active,1:active');
            $table->mediumText('description')->nullable();
            $table->string('size',50)->nullable();
            $table->integer('stock')->default(0);
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
