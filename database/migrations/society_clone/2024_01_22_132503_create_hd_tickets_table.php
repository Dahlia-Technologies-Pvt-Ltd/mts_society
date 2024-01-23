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
        Schema::create('hd_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('hd_master_category_id')->constrained();//no need to society id - we can get this from category table 
            $table->foreignId('hd_master_sub_category_id')->constrained()->default(0)->nullable();
            $table->foreignId('hd_master_further_classification_id')->constrained()->default(0)->nullable();
            $table->string('title')->nullable()->default(null); 
            $table->longText('description')->nullable();
            $table->date('expected_solution_date')->nullable();//calculate based on turn around days
            $table->tinyInteger('status')->default(0)->comment('0-In Pending,1-Progress,2-Completed,3-Rejected,4-Cancelled');           
 		    $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hd_tickets');
    }
};
