<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->onDelete('cascade');
            $table->foreignId('contract_template_id')->constrained('contract_templates');
            $table->string('contract_number')->nullable();
            $table->date('contract_date');
            $table->decimal('transaction_value', 20, 2)->nullable(); // Tăng precision cho số tiền lớn
            $table->longText('content')->nullable(); // Nội dung hợp đồng đã tùy chỉnh
            $table->json('clauses')->nullable(); // Các điều khoản
            $table->longText('testimonial_content')->nullable(); // Lời chứng
            $table->string('notary_fee')->nullable();
            $table->string('notary_number')->nullable();
            $table->string('book_number')->nullable();
            $table->json('additional_info')->nullable(); // Thông tin bổ sung khác
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->timestamps();

            // Indexes
            $table->index(['dossier_id', 'status']);
            $table->index('contract_date');
            $table->index('contract_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
};
