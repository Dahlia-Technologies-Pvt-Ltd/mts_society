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
        Schema::create('master_socities', function (Blueprint $table) {
            $table->id(); 
            $table->string('society_unique_code')->unique();
            $table->string('society_name');
            $table->string('owner_name')->nullable(); 
            $table->string('email');
            $table->string('phone_number', 15)->nullable();
            $table->string('address',100)->nullable();
            $table->string('adress2',100)->nullable();
            $table->foreignId('country_id')->nullable();
            $table->foreignId('state_id')->nullable();
            $table->foreignId('city_id')->nullable();
            $table->string('zipcode')->nullable();            
            $table->string('gst_number',20)->nullable();
            $table->string('pan_number',20)->nullable();
            $table->foreignId('subscription_plan_id')->constrained()->default(0);
            $table->tinyInteger('payment_mode')->default(0)->comment('0-pending,1-cash,2-online,3-cheque,4-RTGS');
            $table->tinyInteger('payment_status')->default(0)->comment('0-pending,1-done');
            //$table->string('data_on_server')->nullable(); 
            $table->string('documents')->nullable();
            //$table->Integer('no_of_employees')->nullable();
            $table->char('currency_code', 4)->nullable();           
            $table->tinyInteger('is_approved')->default(0)->comment('0-Not Approved,1-Approved');//1 - Means
            $table->tinyInteger('status')->default(0)->comment('0-Active,1-Inactive');//)0-Active, 1-Means Inactive
            $table->tinyInteger('is_renewal_plan')->default(1)->comment('0-not renewal plan,1-renewal plan');//1 - Means
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
        Schema::dropIfExists('master_socities');
    }
};
