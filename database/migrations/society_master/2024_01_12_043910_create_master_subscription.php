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
            $table->string('subscription_plan');
            $table->float('price',10,2)->default(0.00);
            $table->integer('frequency')->default(0)->comment('0-nodefine,1-Monthly,3-Qty,6-halfYer,12-yearly');
            $table->string('features')->nullable();
            $table->tinyInteger('is_renewal_plan')->default(1)->comment('0-No_Renewal,1-Renewal');
            $table->tinyInteger('status')->default(0)->comment('0-Active,1-Inactive');//)0-Active, 1-Means Inactive
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
        Schema::dropIfExists('master_subscriptions');
    }
};
