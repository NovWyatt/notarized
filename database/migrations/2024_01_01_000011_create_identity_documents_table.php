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
            $table->unsignedBigInteger('individual_litigant_id');
            $table->enum('document_type', ['cccd', 'cmnd', 'passport', 'officer_id', 'student_card']);
            $table->string('document_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->string('issued_by')->nullable();
            $table->string('school_name')->nullable();
            $table->string('academic_year')->nullable();
            $table->timestamps();

            $table->index('document_type', 'identity_docs_type_idx'); // vẫn giữ index này được
        });

    }

    public function down()
    {
        Schema::dropIfExists('identity_documents');
    }
}
