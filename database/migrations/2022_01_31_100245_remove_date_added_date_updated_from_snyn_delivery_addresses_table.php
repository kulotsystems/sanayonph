<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveDateAddedDateUpdatedFromSnynDeliveryAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('snyn_delivery_addresses', function (Blueprint $table) {
            $table->dropColumn('date_added');
            $table->dropColumn('date_updated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('snyn_delivery_addresses', function (Blueprint $table) {
            $table->timestamp('date_added')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('date_updated')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }
}
