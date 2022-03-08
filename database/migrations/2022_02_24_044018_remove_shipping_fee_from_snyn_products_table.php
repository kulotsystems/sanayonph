<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveShippingFeeFromSnynProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('snyn_products', function (Blueprint $table) {
            $table->dropColumn('shipping_fee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('snyn_products', function (Blueprint $table) {
            $table->unsignedFloat('shipping_fee')->default(0)->after('allows_pickup');
        });
    }
};
