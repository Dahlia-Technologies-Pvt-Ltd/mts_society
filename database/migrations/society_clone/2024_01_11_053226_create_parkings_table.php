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
        Schema::create('parkings', function (Blueprint $table) {
            $table->id();
            $table->string('parking_type')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->foreignId('societies_id')->constrained();
            $table->foreignId('tower_id')->constrained();
            $table->foreignId('wing_id')->constrained()->nullable();
            $table->foreignId('floor_id')->constrained();
            $table->foreignId('flat_id')->constrained();
            $table->string('parking_area_number')->nullable();
            $table->string('parking_name');
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
        Schema::dropIfExists('parkings');
    }
};
