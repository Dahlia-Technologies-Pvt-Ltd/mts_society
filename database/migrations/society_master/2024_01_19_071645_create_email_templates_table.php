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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_code')->unique()->index('template_code');
            $table->string('title')->nullable()->default(null);
            $table->longText('content')->nullable()->default(null);
            $table->string('subject')->nullable()->default(null);
            $table->string('template_variable')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->tinyInteger('status')->default(0)->comment('0-Active,1-Inactive');//0-Active, 1- Inactive
            $table->tinyInteger('sequence')->default(0);
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
