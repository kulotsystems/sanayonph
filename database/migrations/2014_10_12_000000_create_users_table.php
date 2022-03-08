<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snyn_users', function (Blueprint $table) {
            $table->mediumIncrements('id');

            $table->string('first_name' , 64);
            $table->string('middle_name', 64)->nullable();
            $table->string('last_name'  , 64);
            $table->string('name_suffix', 32)->nullable();
            $table->boolean('gender')->default(0);
            $table->date('date_of_birth');

            $table->string('email', 128)->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('mobile', 32)->unique()->nullable();
            $table->timestamp('mobile_verified_at')->nullable();

            $table->string('username', 32)->unique();
            $table->string('password', 255);

            $table->string('avatar', 255)->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('snyn_users');
    }
}
