<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_guides', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('icon', 50)->default('file-earmark-text'); // nama Bootstrap Icons
            $table->string('summary', 300);
            $table->json('steps');
            $table->json('tips')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Pivot: dokumen wajib untuk setiap beasiswa.
        Schema::create('document_guide_scholarship', function (Blueprint $table) {
            $table->foreignId('document_guide_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();

            $table->primary(['document_guide_id', 'scholarship_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_guide_scholarship');
        Schema::dropIfExists('document_guides');
    }
};
