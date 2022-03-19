<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveUserIdFromSnynReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('snyn_reviews', function (Blueprint $table) {
            if(DB::select(DB::raw('SHOW KEYS FROM snyn_reviews WHERE key_name=\'snyn_reviews_user_id_foreign\'')))
                $table->dropForeign('snyn_reviews_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('snyn_reviews', function (Blueprint $table) {
            Schema::table('snyn_reviews', function (Blueprint $table) {
                $table->unsignedMediumInteger('user_id')->after('id');
            });
        });
    }
};
