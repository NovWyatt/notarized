<?php

// 2024_01_01_0000018_create_apartments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('apartment_number', 50)->nullable();
            $table->integer('apartment_floor')->nullable();
            $table->integer('building_floors')->nullable();
            $table->decimal('construction_area', 10, 2)->nullable();
            $table->decimal('floor_area', 10, 2)->nullable();
            $table->string('ownership_form')->nullable();
            $table->date('ownership_term')->nullable();
            $table->string('structure')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('asset_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('apartments');
    }
};
