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
            $table->string('master_socities_id',0)->default(0)->nullable();
            $table->foreignId('user_type_id')->constrained()->default(0)->nullable();
            $table->foreignId('user_sub_type_id')->constrained()->default(0)->nullable();
            //$table->integer('usertype')->default(0);
            //$table->integer('usersubtype')->default(0);
            $table->string('full_name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->nullable()->unique();
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
            $table->foreignId('city')->constrained()->default(0)->nullable();
            $table->string('zipcode')->nullable()->default(null);
            $table->string('country_code')->nullable()->default(null);
            $table->tinyInteger('is_approv')->default(0)->comment('0-Not_Approve,1-Approve');//)0-Not_Approve, 1-Means Approve 
            //$table->tinyInteger('is_paid')->default(0)->comment('0-Not_Paid,1-Paid');//)0-Not_Paid, 1-Means paid 
            //$table->tinyInteger('plan_id')->default(0)->comment('0-Not_Paid,1-Paid');//)0-Not_Paid, 1-Means paid 
            $table->tinyInteger('multilogin')->default(0)->comment('0-all,1-desktop,2-mobile');//)0-all,1-desktop,2-mobile 
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
