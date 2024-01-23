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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('mail_through_ip')->default(0)->comment('1-Mail through IP)');
            $table->string('mail_mailer',50)->nullable()->default(null);
            $table->string('mail_host',255)->nullable()->default(null);
            $table->string('mail_port',10)->nullable()->default(null);
            $table->string('mail_username',50)->nullable()->default(null);
            $table->string('mail_password',255)->nullable()->default(null);
            $table->string('mail_from_address',50)->nullable()->default(null);
            $table->string('mail_from_name',50)->nullable()->default(null);
            $table->string('mail_ssl_enable',5)->nullable()->default(null);
            // $table->string('google_analytics_key')->nullable()->default(null);
            // $table->string('google_location_key')->nullable()->default(null);
            // $table->string('company_logo',100)->nullable()->default(null);
            $table->string('support_email',255)->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
