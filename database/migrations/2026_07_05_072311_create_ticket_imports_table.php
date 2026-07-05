<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_imports', function (Blueprint $table) {
            $table->id();
            $table->string('excel_path');
            $table->string('zip_path');
            $table->unsignedInteger('zip_pdf_count')->default(0);
            $table->unsignedInteger('mapped_ticket_count')->default(0);
            $table->unsignedInteger('missing_pdf_count')->default(0);
            $table->unsignedInteger('unmapped_pdf_count')->default(0);
            $table->json('warnings')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_imports');
    }
};
