<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 11: Create identity documents table
// database/migrations/2024_01_01_000011_create_identity_documents_table.php

class CreateIdentityDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('identity_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('individual_litigant_id')->constrained('individual_litigants')->onDelete('cascade');
            $table->enum('document_type', ['cccd', 'cmnd', 'passport', 'officer_id', 'student_card']);
            $table->string('document_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->string('issued_by')->nullable();
            // Thêm trường đặc biệt cho thẻ học sinh
            $table->string('school_name')->nullable(); // Tên trường học
            $table->string('academic_year')->nullable(); // Niên khóa
            $table->timestamps();

            $table->index('individual_litigant_id', 'identity_docs_individual_idx');
            $table->index('document_type', 'identity_docs_type_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('identity_documents');
    }
}
