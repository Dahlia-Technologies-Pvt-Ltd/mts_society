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
            $table->string('user_code')->unique();
            $table->string('username')->nullable()->default(null)->unique();
            $table->string('email')->unique();
            $table->string('password')->nullable()->default(null);
            $table->string('phone_number', 15)->unique();
            $table->string('master_society_ids')->nullalbe()->default(0);
            $table->enum('gender', ['Male','Female'])->nullable()->default(null);
            $table->string('address')->nullable()->default(null);
            $table->foreignId('country_id')->constrained()->default(0)->nullable();
            $table->foreignId('state_id')->constrained()->default(0)->nullable();  
            $table->foreignId('city_id')->constrained()->default(0)->nullable();
            $table->string('zipcode')->nullable()->default(null);
            $table->tinyInteger('usertype')->default(0)->comment('0-every other user,1-admin,2-superadmin');;
            $table->tinyInteger('status')->default(0)->comment('0-Active,1-Inactive,2-Blocked');
            $table->timestamp('blocked_at')->nullable()->default(null);
            $table->string('profile_picture',255)->nullable()->default(null);
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
        Schema::dropIfExists('master_users');
    }
};
