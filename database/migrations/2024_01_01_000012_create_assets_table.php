<?php

// 2024_01_01_0000012_create_assets_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->enum('asset_type', [
                'real_estate_house',
                'real_estate_apartment',
                'real_estate_land_only',
                'movable_property_car',
                'movable_property_motorcycle'
            ]);
            $table->string('asset_name')->nullable();
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('asset_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
};
