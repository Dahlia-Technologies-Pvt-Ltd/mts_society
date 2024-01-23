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
        Schema::create('master_service_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('societies_id')->constrained();
            $table->string('name')->nullable(); 
            $table->tinyInteger('is_daily_helper')->default(0)->comment('0-No,1-Yes');
            $table->tinyInteger('status')->default(0)->comment('0-Active,1-Inactive');//0-Active, 1-Means Inactive
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_service_providers');
    }
};
