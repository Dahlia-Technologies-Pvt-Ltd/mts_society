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
            $table->string('email')->nullable()->default(null);
            $table->string('phone_number', 15)->nullable()->default(null);
            $table->string('address',100)->nullable();
            $table->string('adress2',100)->nullable()->default(null);
            $table->foreignId('country_id')->constrained()->default(0)->nullable();
            $table->foreignId('state_id')->constrained()->default(0)->nullable();  
            $table->foreignId('city_id')->constrained()->default(0)->nullable();
            $table->string('zipcode')->nullable();            
            $table->string('gst_number',20)->nullable()->default(null);
            $table->string('pan_number',20)->nullable()->default(null);            
            //$table->string('documents')->nullable();
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
        Schema::dropIfExists('master_socities');
    }
};
