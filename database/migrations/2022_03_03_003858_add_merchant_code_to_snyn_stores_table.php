<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMerchantCodeToSnynStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('snyn_stores', function (Blueprint $table) {
            $table->string('merchant_code', 16)->nullable()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('snyn_stores', function (Blueprint $table) {
            $table->dropColumn('merchant_code');
        });
    }
};
