<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('otp_varifications', function (Blueprint $table) {
            $table->id();
            $table->integer('society_id')->default(0);
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->integer('country_code');
            $table->string('phone_number', 15);
            $table->string('otp', 10)->nullable();
            $table->tinyInteger('status')->default(0)->comment('0-New,1-Resent'); //1 - Means Inactive
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_varifications');
    }
};
