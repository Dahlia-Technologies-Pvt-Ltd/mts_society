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
        Schema::create('master_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name')->nullable()->default(null);
            $table->decimal('plan_price',15,2)->default(0.00); 
            $table->timestamp('plan_start_date')->nullable()->default(null);
            $table->timestamp('plan_end_date')->nullable()->default(null);
            $table->string('features')->nullable()->default(null);
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
        Schema::dropIfExists('master_subscriptions');
    }
};
