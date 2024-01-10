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
        Schema::create('master_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->string('phone_number', 15)->nullable()->default(null);
            // $table->foreignId('master_society_id',0)->constrained()->default(0)->nullable();
            $table->string('master_society_ids')->nullable();
            $table->enum('gender', ['Male','Female'])->nullable()->default(null);
            // $table->unsignedBigInteger('towerid')->default(0);
            // $table->unsignedBigInteger('wingid')->nullable()->default(null);
            // $table->unsignedBigInteger('floorid')->default(0);
            // $table->unsignedBigInteger('flatid')->default(0);
            $table->string('street_address')->nullable()->default(null);
            $table->foreignId('country')->constrained()->default(0)->nullable();
            $table->foreignId('state')->constrained()->default(0)->nullable();  
            $table->foreignId('city')->constrained()->default(0)->nullable();
            $table->string('zipcode')->nullable()->default(null);
            $table->tinyInteger('user_type')->default(0)->comment('0-other user,1-admin,2-super admin');
            $table->tinyInteger('status')->default(0)->comment('0-Active,1-Inactive');//)0-Active, 1-Means Inactive        
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
        Schema::dropIfExists('master_users');
    }
};
