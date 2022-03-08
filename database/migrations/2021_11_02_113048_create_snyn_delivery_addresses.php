<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSnynDeliveryAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snyn_delivery_addresses', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->unsignedSmallInteger('barangay_id');
            $table->unsignedMediumInteger('user_id');
            $table->string('street', 128);
            $table->timestamp('date_added')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('date_updated')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('snyn_delivery_addresses');
    }
}
