<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contract_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('contract_type_id')->constrained('contract_types')->onDelete('cascade');
            $table->longText('content')->nullable(); // Nội dung template HTML
            $table->json('template_settings')->nullable(); // Cài đặt hiển thị các phần
            $table->json('template_info')->nullable(); // Thông tin tạm thời (văn phòng, người tạo...)
            $table->json('default_clauses')->nullable(); // Điều khoản mặc định
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['contract_type_id', 'is_active']);
            $table->index('sort_order');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_templates');
    }
};
