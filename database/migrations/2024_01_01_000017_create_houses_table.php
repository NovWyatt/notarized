<?php

// 2024_01_01_0000017_create_houses_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('house_type')->nullable();
            $table->decimal('construction_area', 10, 2)->nullable();
            $table->decimal('floor_area', 10, 2)->nullable();
            $table->string('ownership_form')->nullable();
            $table->string('grade_level', 50)->nullable();
            $table->integer('number_of_floors')->nullable();
            $table->date('ownership_term')->nullable();
            $table->string('structure')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('asset_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('houses');
    }
};
