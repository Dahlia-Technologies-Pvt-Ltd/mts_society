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
        Schema::create('soc_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('societies_id')->constrained();
            $table->tinyInteger('document_type')->default(0)->comment('0-Resident,1-Society,2-Admin');
            $table->string('name')->nullable()->default(null);
            $table->string('attachments','1000')->nullable()->default(null);

            $table->foreignId('tower_id')->constrained()->default(0)->nullable();
            $table->foreignId('wing_id')->constrained()->default(0)->nullable();
            $table->foreignId('flat_id')->constrained()->default(0)->nullable(); 

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
        Schema::dropIfExists('soc_documents');
    }
};
