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
        Schema::create('master_floors', function (Blueprint $table) {
            $table->id();
            $table->string('floor_name');
            $table->foreignId('master_tower_id')->constrained();
            $table->foreignId('master_wing_id')->constrained()->nullable();
            $table->tinyInteger('status')->default(0)->comment('0-Active,1-Inactive');//)0-Active, 1-Means Inactive                  
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
 		    $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_floors');
    }
};
