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
        Schema::create('brothers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_family_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_under_12')->default(false);
            $table->boolean('is_over_75')->default(false);
            $table->boolean('has_locomotion_need')->default(false);
            $table->boolean('has_mobility_need')->default(false);
            $table->boolean('normal_ticket')->default(false);
            $table->boolean('andante')->default(false);
            $table->boolean('distico')->default(false);
            $table->json('source_row')->nullable();
            $table->timestamps();

            $table->unique(['service_group_id', 'ticket_family_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brothers');
    }
};
