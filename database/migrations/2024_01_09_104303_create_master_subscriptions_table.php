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
            $table->foreignId('subscription_plan_id')->constrained()->default(0);
            $table->foreignId('master_user_id')->constrained()->default(0);
            $table->foreignId('master_socities_id')->constrained()->default(0);
            $table->float('Plan_price',10,2)->default(0.00);
            $table->dateTime('plan_start_date')->nullable();
            $table->dateTime('plan_end_date')->nullable();
            $table->integer('frequency')->default(0)->comment('0-nodefine,1-Monthly,3-Qty,6-halfYer,12-yearly');
            $table->tinyInteger('status')->default(0)->comment('0-Active,1-Inactive,2-Completed');
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
