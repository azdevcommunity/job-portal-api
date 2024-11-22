<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable(false);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->boolean('is_remote')->default(false);
            $table->enum('job_type', ['full-time', 'part-time', 'contract', 'internship']);
            $table->string('seniority_level');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->text('description');
            $table->text('job_overview');
            $table->string('job_role');
            $table->text('job_responsibilities');
            $table->text('you_have_text')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
