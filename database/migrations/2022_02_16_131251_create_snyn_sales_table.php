<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSnynSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snyn_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');

            $table->string('product_name');
            $table->text('product_description')->nullable();
            $table->dateTime('product_published_at')->nullable();
            $table->string('product_image', 128);
            $table->unsignedDouble('product_price');
            $table->unsignedSmallInteger('product_stock');
            $table->string('product_label');
            $table->string('category_name');
            $table->unsignedTinyInteger('service_fee_percentage');

            $table->unsignedSmallInteger('quantity');
            $table->unsignedDouble('price_after_quantity');
            $table->unsignedFloat('shipping_fee_after_quantity')->default(0);
            $table->unsignedDouble('price_after_shipping');
            $table->unsignedDouble('service_fee_after_shipping');
            $table->unsignedDouble('price_after_service_fee');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('order_id')->references('id')->on('snyn_orders')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('product_id')->references('id')->on('snyn_products')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('snyn_sales');
    }
};
