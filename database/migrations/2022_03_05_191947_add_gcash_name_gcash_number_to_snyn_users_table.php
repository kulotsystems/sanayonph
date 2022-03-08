<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('snyn_users', function (Blueprint $table) {
            $table->string('gcash_name', 128)->nullable()->after('avatar');
            $table->string('gcash_number', 11)->nullable()->after('gcash_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('snyn_users', function (Blueprint $table) {
            $table->dropColumn('gcash_name');
            $table->dropColumn('gcash_number');
        });
    }
};
