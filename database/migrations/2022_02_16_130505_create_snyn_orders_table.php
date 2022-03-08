<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSnynOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snyn_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedMediumInteger('user_id');
            $table->unsignedInteger('store_id');
            $table->unsignedTinyInteger('delivery_method_id');
            $table->unsignedTinyInteger('payment_method_id');

            $table->unsignedInteger('delivery_address_id')->nullable();
            $table->string('delivery_address');
            $table->string('delivery_fullname')->nullable();
            $table->string('delivery_mobile')->nullable();
            $table->string('delivery_email')->nullable();

            $table->dateTime('ordered_at');
            $table->dateTime('confirmed_by_seller_at')->nullable();
            $table->dateTime('cancelled_by_buyer_at')->nullable();
            $table->text('reason_by_buyer')->nullable();
            $table->dateTime('declined_by_seller_at')->nullable();
            $table->text('reason_by_seller')->nullable();

            $table->string('payment_screenshot', 128)->nullable();
            $table->dateTime('payment_screenshot_at')->nullable();
            $table->dateTime('payment_confirmed_at')->nullable();
            $table->dateTime('payment_declined_at')->nullable();
            $table->text('payment_declined_remarks')->nullable();

            $table->dateTime('shipped_at')->nullable();
            $table->string('courier')->nullable();
            $table->string('tracking_code')->nullable();
            $table->dateTime('pickup_at')->nullable();
            $table->string('pickup_location')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('user_id')->references('id')->on('snyn_users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('store_id')->references('id')->on('snyn_stores')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('delivery_method_id')->references('id')->on('snyn_delivery_methods')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('payment_method_id')->references('id')->on('snyn_payment_methods')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('snyn_orders');
    }
};
