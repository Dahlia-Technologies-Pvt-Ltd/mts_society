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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_subscription_id')->constrained()->default(0);
            $table->foreignId('master_user_id')->constrained()->default(0);
            $table->foreignId('master_socities_id')->constrained()->default(0);
            
            //columns from master table
            $table->string('subscription_plan');
            $table->float('price',10,2)->default(0.00);
            $table->integer('frequency')->default(0)->comment('0-nodefine,1-Monthly,3-Qty,6-halfYer,12-yearly');
            $table->string('features')->nullable();
            $table->tinyInteger('is_renewal_plan')->default(1)->comment('0-No_Renewal,1-Renewal');
           
            $table->tinyInteger('status')->default(0)->comment('0-Active,1-Inactive,2-Completed');
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
