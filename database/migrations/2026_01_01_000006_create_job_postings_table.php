<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('slug')->unique();
            $table->string('summary', 220)->nullable();
            $table->text('description');
            $table->text('requirements');
            $table->string('location');
            $table->string('job_type');
            $table->string('work_mode');
            $table->unsignedBigInteger('salary_min')->nullable();
            $table->unsignedBigInteger('salary_max')->nullable();
            $table->boolean('show_salary')->default(false);
            $table->date('deadline_at')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['status', 'deadline_at']);
            $table->index(['company_profile_id', 'status']);
        });

        Schema::create('job_skill', function (Blueprint $table) {
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->primary(['job_posting_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_skill');
        Schema::dropIfExists('job_postings');
    }
};
