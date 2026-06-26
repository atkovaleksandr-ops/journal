<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->onDelete('cascade');

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->onDelete('cascade');

            $table->foreignId('teacher_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->date('date');

            $table->enum('status', [
                'present',
                'absent',
            ])->default('present');

            $table->text('note')->nullable();

            $table->timestamps();

            // Один студент по одному предмету в один день — одна отметка посещаемости
            $table->unique(['student_id', 'subject_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
