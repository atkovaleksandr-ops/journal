<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            // Какой учитель ведёт предмет
            $table->foreignId('teacher_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Для какой группы этот предмет
            $table->foreignId('group_id')
                ->constrained('groups')
                ->onDelete('cascade');

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};