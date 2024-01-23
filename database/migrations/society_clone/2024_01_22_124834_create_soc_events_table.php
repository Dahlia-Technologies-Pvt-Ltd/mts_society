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
        Schema::create('soc_events', function (Blueprint $table) {
            $table->foreignId('societies_id')->constrained();
            $table->string('title')->nullable();             
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('opening_time', $precision = 0)->nullable();
            $table->time('closing_time', $precision = 0)->nullable();
            $table->longText('description')->nullable(); 
            $table->string('attachments','1000')->nullable()->default(null);
            $table->tinyInteger('status')->default(0)->comment('0-Active,1-Inactive');//0-Active, 1-Means Inactive
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soc_events');
    }
};
