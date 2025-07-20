<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contract_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');
            $table->foreignId('litigant_id')->constrained('litigants');
            $table->string('party_type'); // 'transferor', 'transferee', 'buyer', 'seller', etc.
            $table->string('group_name'); // 'Bên A', 'Bên B', 'Bên thứ nhất', etc.
            $table->integer('order_in_group')->default(1); // Thứ tự trong nhóm
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['contract_id', 'group_name', 'order_in_group']);
            $table->index('litigant_id');

            // Constraint để đảm bảo mỗi litigant chỉ xuất hiện 1 lần trong 1 contract
            $table->unique(['contract_id', 'litigant_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_parties');
    }
};
