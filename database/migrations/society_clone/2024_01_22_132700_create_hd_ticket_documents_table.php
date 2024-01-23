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
        Schema::create('hd_ticket_documents', function (Blueprint $table) {
            $table->id();            
            $table->foreignId('hd_ticket_id')->constrained();           
            $table->string('title')->nullable()->default(null); 
            $table->longText('description')->nullable();
            $table->string('attachments','1000')->nullable()->default(null);
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
        Schema::dropIfExists('hd_ticket_documents');
    }
};
