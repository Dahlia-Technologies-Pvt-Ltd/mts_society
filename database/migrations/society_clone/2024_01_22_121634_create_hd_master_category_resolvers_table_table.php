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
            $table->foreignId('hd_master_sub_category_id')->nullable()->default(null)->
            constrained();//we are not using this right now
            $table->foreignId('user_id')->constrained()->default(0)->nullable();  //Resolver*
            $table->string('master_service_provider_ids')->nullable()->default(null); //array - Service Provided by resolver
            // $table->foreignId('spare_item_management_id')->nullable()->default(null)->references('id')->on('spare_item_managements')->constrained();
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
