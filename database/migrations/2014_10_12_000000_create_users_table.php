<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('password')->default(Hash::make('12345678'));
            $table->timestamp('email_verified_at')->nullable();
            $table->bigInteger('role_id')->unsigned()->default(4);
            $table->bigInteger('company_id')->unsigned()->default(1);
            $table->bigInteger('access_level')->default(3);
            $table->boolean('deleted')->default(false);
            $table->boolean('notified')->default(false);
            $table->boolean('active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
