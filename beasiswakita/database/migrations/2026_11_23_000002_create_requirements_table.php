<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->enum('category', ['akademik', 'administrasi', 'umum']);
            $table->string('description', 500);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['scholarship_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
