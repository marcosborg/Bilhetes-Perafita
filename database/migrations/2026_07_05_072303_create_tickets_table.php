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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ticket_family_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brother_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pdf_filename')->unique();
            $table->string('pdf_path')->nullable();
            $table->string('internal_code')->nullable()->unique();
            $table->string('public_token', 80)->unique();
            $table->string('status')->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->json('source_row')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['service_group_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
