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
            $table->id(); // Primary key
            $table->string('name'); // Name field
            $table->string('number'); // Number field
            $table->string('company_name'); // Company Name field
            $table->string('email'); // Email field
            $table->text('location')->nullable(); // Location field (nullable)
            $table->text('some_text')->nullable(); // Additional text field (nullable)
            $table->string('cr_file')->nullable(); // File upload field for CR (nullable)
            $table->string('cc_file')->nullable(); // File upload field for CC (nullable)
            $table->string('tl_file')->nullable(); // File upload field for TL (nullable)
            $table->string('processing_id')->nullable();
            $table->string('address')->nullable(); // Level 2 field
            $table->string('level')->default('1'); // Tracks the current level
            $table->string('file_path')->nullable(); // Level 3 field (file uploaded)
            // Additional fields
            $table->unsignedBigInteger('created_by')->nullable(); // Created by user ID
            $table->unsignedBigInteger('updated_by')->nullable(); // Updated by user ID
            $table->unsignedBigInteger('deleted_by')->nullable(); // Deleted by user ID
            $table->unsignedBigInteger('user_id')->nullable(); // Associated user ID
            $table->tinyInteger('status')->default(1); // Status (default active)
            $table->softDeletes(); // Soft delete column

            $table->timestamps(); // Created_at and Updated_at fields

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
