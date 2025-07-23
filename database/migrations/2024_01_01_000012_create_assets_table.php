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
                'movable_property_motorcycle',
            ]);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('asset_type');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            // Add indexes for performance
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
};
