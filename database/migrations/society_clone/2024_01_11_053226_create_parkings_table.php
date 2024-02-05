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
            $table->foreignId('societies_id')->constrained();
            $table->tinyInteger('parking_type')->nullable()->default(0)->comment('0-Resident Parking,1-Visitors Parking'); // Keep this in Config
            $table->tinyInteger('vehicle_type')->nullable()->default(2)->comment('2-Wheeler,4-Wheeler');            
            $table->foreignId('tower_id')->constrained();
            $table->foreignId('wing_id')->constrained()->nullable()->default(0);
            $table->foreignId('floor_id')->constrained()->nullable()->default(0);
            $table->string('parking_area_number',50)->nullable();
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
