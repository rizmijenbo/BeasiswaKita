<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('organizer');
            $table->enum('level', ['SMP', 'SMA', 'Kuliah', 'Umum'])->index();
            $table->string('summary', 300);
            $table->text('description');
            $table->json('benefits');
            $table->date('open_date');
            $table->date('deadline')->index();
            $table->string('official_url');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
