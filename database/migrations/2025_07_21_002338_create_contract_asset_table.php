<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contract_asset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets');
            $table->text('notes')->nullable(); // Ghi chú về tài sản trong hợp đồng
            $table->timestamps();

            // Indexes
            $table->index('contract_id');
            $table->index('asset_id');

            // Constraint để đảm bảo mỗi asset chỉ xuất hiện 1 lần trong 1 contract
            $table->unique(['contract_id', 'asset_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_asset');
    }
};
