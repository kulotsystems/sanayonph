<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPublishedPublishedAtToSnynProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('snyn_products', function (Blueprint $table) {
            $table->boolean('is_published')->default(0)->after('price_stock_mode');
            $table->dateTime('published_at')->nullable()->after('is_published');
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
            $table->dropColumn('is_published');
            $table->dropColumn('published_at');
        });
    }
}
