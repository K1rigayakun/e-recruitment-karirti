<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('applicant_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cv_document_id')->constrained('documents')->restrictOnDelete();
            $table->string('cover_letter', 1500)->nullable();
            $table->string('status')->default('applied');
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->text('company_note')->nullable();
            $table->timestamps();

            $table->unique(['job_posting_id', 'applicant_profile_id']);
            $table->index('status');
            $table->index('applicant_profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
