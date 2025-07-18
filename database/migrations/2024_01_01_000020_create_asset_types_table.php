<?php

// 2024_01_01_0000020_create_asset_types_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('asset_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default asset types
        DB::table('asset_types')->insert([
            [
                'code' => 'real_estate_house',
                'name' => 'Bất động sản / Đất có tài sản gắn liền / Nhà',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'real_estate_apartment',
                'name' => 'Bất động sản / Đất có tài sản gắn liền / Căn hộ',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'real_estate_land_only',
                'name' => 'Bất động sản / Đất không có tài sản gắn liền trên đất',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'movable_property_car',
                'name' => 'Động sản / Phương tiện GT đường bộ / Ô tô',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'movable_property_motorcycle',
                'name' => 'Động sản / Phương tiện GT đường bộ / Mô tô - xe máy',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('asset_types');
    }
};
