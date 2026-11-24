<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->unsignedTinyInteger('days_before')->default(7);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->unique(['scholarship_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
