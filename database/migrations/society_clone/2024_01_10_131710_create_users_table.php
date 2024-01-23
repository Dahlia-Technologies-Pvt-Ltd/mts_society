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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_user_id')->constrained()->default(0);
            $table->string('master_society_ids')->nullable();
            $table->foreignId('user_type_id')->constrained()->default(0)->nullable();
            $table->foreignId('user_sub_type_id')->constrained()->default(0)->nullable();
            //$table->integer('usertype')->default(0);
            //$table->integer('usersubtype')->default(0);
            $table->string('full_name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->nullable()->unique();
            $table->string('country_code')->nullable()->default(null);
            $table->string('phone_number', 15)->nullable()->default(null);
            $table->enum('gender', ['Male','Female'])->nullable()->default(null);
            $table->unsignedBigInteger('towerid')->default(0);
            $table->unsignedBigInteger('wingid')->nullable()->default(null);
            $table->unsignedBigInteger('floorid')->default(0);
            $table->unsignedBigInteger('flatid')->default(0);
            $table->string('image')->nullable()->default(null);
            $table->string('street_address')->nullable()->default(null);
            $table->foreignId('country')->constrained()->default(0)->nullable();
            $table->foreignId('state')->constrained()->default(0)->nullable();  
            $table->string('city', 50)->nullable()->default(null);
            $table->string('zipcode')->nullable()->default(null);            
            $table->tinyInteger('is_approved')->default(0)->comment('0-Not_Approve,1-Approve');//)0-Not_Approve, 1-Means Approve-mobile 
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
        Schema::dropIfExists('users');
    }
};
