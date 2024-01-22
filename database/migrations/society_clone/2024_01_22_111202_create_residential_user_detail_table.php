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
        Schema::create('residential_user_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('societies_id')->constrained();
            $table->foreignId('user_id')->constrained()->default(0)->nullable();
            $table->foreignId('flat_id')->constrained()->nullable()->default(0);
            $table->string('vehicle_types')->nullable(); // array [2,4] -  2 - wheeler / 4 - wheeler
            $table->string('parking_ids')->nullable(); // array ex [2,3,4]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residential_user_detail');
    }
};
