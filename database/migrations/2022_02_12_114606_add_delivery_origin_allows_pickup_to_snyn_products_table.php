<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryOriginAllowsPickupToSnynProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('snyn_products', function (Blueprint $table) {
            $table->unsignedInteger('delivery_origin')->after('published_at');
            $table->boolean('allows_pickup')->default(0)->after('delivery_origin');

            $table->foreign('delivery_origin')->references('id')->on('snyn_delivery_addresses')->onUpdate('cascade')->onDelete('restrict');
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
            $table->dropForeign('snyn_products_delivery_origin_foreign');
            $table->dropColumn('delivery_origin');
            $table->dropColumn('allows_pickup');
        });
    }
};
