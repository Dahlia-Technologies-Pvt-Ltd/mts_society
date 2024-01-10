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
        Schema::create('master_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name',25);
            $table->char('currency_code', 4)->nullable();
            $table->unsignedBigInteger('number_code')->nullable();
            $table->float('notrate',10,2)->default(0.00);
            $table->tinyInteger('isbasecurrency')->nullable()->default(0);
            $table->tinyInteger('status')->nullable()->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_currencies');
    }
};
