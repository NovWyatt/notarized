<?php

// 2024_01_01_0000016_create_land_plots_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('land_plots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('plot_number', 50)->nullable();
            $table->string('map_sheet_number', 50)->nullable();
            $table->string('house_number', 20)->nullable();
            $table->string('street_name')->nullable();
            $table->string('province', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('ward', 100)->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->string('usage_form')->nullable();
            $table->string('usage_purpose')->nullable();
            $table->date('land_use_term')->nullable();
            $table->string('usage_origin')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('asset_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('land_plots');
    }
};
