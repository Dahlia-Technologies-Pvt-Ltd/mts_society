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
        Schema::create('non_residential_user_details', function (Blueprint $table) {
            $table->id();
            $table->string('society_ids')->nullable();// array -  Service Providers / External Facility Manager can provide service to multiple socities
            $table->foreignId('user_id')->constrained()->default(0)->nullable(); // This can be NULL in case of Service Providers / External Facility Manager
            $table->string('full_name')->nullable()->default(null); // if user id exists, take value from user's table
            $table->string('email')->nullable()->unique()->nullable()->default(null); // if user id exists, take value from user's table
            $table->string('country_code')->nullable()->default(null);// if user id exists, take value from user's table
            $table->string('phone_number', 15)->nullable()->default(null);// if user id exists, take value from user's table

            //Address Fields
            $table->string('street_address')->nullable()->default(null);
            $table->foreignId('country')->constrained()->default(0)->nullable();
            $table->foreignId('state')->constrained()->default(0)->nullable();  
            $table->string('city', 50)->nullable()->default(null);
            $table->string('zipcode')->nullable()->default(null); 
            
            //Aadhar / PAN number
            $table->string('aadhaar_no')->nullable();
            $table->string('pan_no')->unique();//PAN

            //Service Provider specifi fields //
            $table->string('master_service_provider_ids')->nullable();// array - can be multiple ex [2,3,4]
           

            //Facility Manager Specific Fields
            $table->string('management_company_name')->nullable()->default(null);
            $table->string('management_company_country_code')->nullable()->default(null);
            $table->string('management_company_phone_number', 15)->nullable()->default(null);
            $table->string('assigned_tower_ids')->nullable();// array - can be multiple ex [2,3,4]

            $table->tinyInteger('team_type')->default(0)->comment('0-Facility Manager,1-Service Provider,2-Security Guard');
            
            //-------------
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
        Schema::dropIfExists('non_residential_user_details');
    }
};
