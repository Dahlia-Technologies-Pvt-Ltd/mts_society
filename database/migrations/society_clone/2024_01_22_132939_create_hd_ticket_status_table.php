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
        Schema::create('hd_ticket_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hd_ticket_id')->constrained();           
            $table->string('title')->nullable()->default(null); 
            $table->longText('description')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0-In Pending,1-Progress,2-Completed,3-Rejected,4-Cancelled');       
            $table->foreignId('user_id')->constrained()->default(0)->nullable(); 
 		    $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hd_ticket_status');
    }
};
