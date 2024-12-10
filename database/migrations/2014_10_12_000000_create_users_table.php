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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // auto-increment primary key
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('mobilenumber')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('usertype')->default('user');
            $table->date('expiredate')->nullable();
            $table->string('status')->default('active');
            $table->tinyInteger('approve_status')->default(0);
            $table->string('user_type')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps(); // created_at and updated_at columns
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
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
};
