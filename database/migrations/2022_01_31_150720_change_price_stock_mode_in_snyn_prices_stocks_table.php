<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePriceStockModeInSnynPricesStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('snyn_prices_stocks', function (Blueprint $table) {
            DB::statement("ALTER TABLE snyn_prices_stocks MODIFY price_stock_mode ENUM('var1_only', 'var2_only', 'both_vars') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('snyn_prices_stocks', function (Blueprint $table) {
            DB::statement("ALTER TABLE snyn_prices_stocks MODIFY price_stock_mode ENUM('var1-only', 'var2-only', 'both-vars') NOT NULL");
        });
    }
}
