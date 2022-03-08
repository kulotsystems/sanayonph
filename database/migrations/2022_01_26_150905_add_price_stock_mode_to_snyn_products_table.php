<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceStockModeToSnynProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('snyn_products', function (Blueprint $table) {
            $table->enum('price_stock_mode', ['var1-only', 'var2-only', 'both-vars'])->after('description');
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
            $table->dropColumn('price_stock_mode');
        });
    }
}
