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
        Schema::create('leads', function (Blueprint $table) {
            $table->id(); // auto-increment primary key
            $table->string('name');
            $table->string('phone_number');
            $table->string('company_name');
            $table->string('email');
            $table->text('location')->nullable();
            $table->text('some_text')->nullable();
            $table->string('cr_file')->nullable();
            $table->string('cc_file')->nullable();
            $table->string('tl_file')->nullable();
            $table->string('processing_id')->nullable();
            $table->string('address')->nullable();
            $table->string('level')->nullable();
            $table->string('lead_type')->default('1');
            $table->string('change_status')->nullable();
            $table->tinyInteger('approve_status')->default(0);
            $table->string('service_type')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('agent_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps(); // created_at and updated_at // Created_at and Updated_at fields

            // Foreign key constraints (if needed)
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
