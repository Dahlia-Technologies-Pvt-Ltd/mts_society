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
        Schema::create('hd_master_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('societies_id')->constrained();
            $table->string('category_name')->nullable()->default(null);
            $table->string('image')->nullable()->default(null); 
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
        Schema::dropIfExists('hd_master_categories');
    }
};
