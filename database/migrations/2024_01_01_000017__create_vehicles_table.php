<?php

// 2024_01_01_0000017_create_vehicles_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('registration_number', 50)->nullable();
            $table->string('issuing_authority')->nullable();
            $table->date('issue_date')->nullable();
            $table->string('license_plate', 20)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('vehicle_type', 100)->nullable();
            $table->string('color', 50)->nullable();
            $table->decimal('payload', 8, 2)->nullable();
            $table->string('engine_number', 50)->nullable();
            $table->string('chassis_number', 50)->nullable();
            $table->string('type_number', 50)->nullable();
            $table->decimal('engine_capacity', 8, 2)->nullable();
            $table->integer('seating_capacity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('asset_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};
