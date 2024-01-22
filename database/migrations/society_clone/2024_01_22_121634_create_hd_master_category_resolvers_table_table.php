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
        Schema::create('hd_master_category_resolvers_table', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hd_master_category_id')->constrained();//no need to society id - we can get this from category table
            $table->foreignId('hd_master_sub_category_id')->constrained()->default(0)->nullable();//we are not using this right now
            $table->foreignId('non_residential_user_detail_id')->constrained()->default(0)->nullable();  //Resolver*
            $table->string('service_provider_master_ids')->nullable()->default(null); //array - Service Provided by resolver

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hd_master_category_resolvers_table');
    }
};
