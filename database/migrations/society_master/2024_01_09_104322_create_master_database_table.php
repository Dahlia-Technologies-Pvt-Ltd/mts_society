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
        Schema::create('master_database', function (Blueprint $table) {
            $table->id();
            $table->text('databasename');
            $table->text('databaseuid');
            $table->text('databasepwd');
            //$table->string('useruid');
            $table->foreignId('master_user_id')->constrained()->default(0);
            $table->foreignId('master_socities_id')->constrained()->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_database');
    }
};
