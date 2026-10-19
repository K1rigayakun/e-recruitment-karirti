<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('nim', 20)->nullable();
            $table->string('study_program')->default('D3 Teknik Informatika');
            $table->smallInteger('graduation_year')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('headline', 120)->nullable();
            $table->text('about')->nullable();
            $table->string('github_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->foreignId('current_cv_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->boolean('is_public')->default(false);
            $table->timestamp('alumni_verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_profiles');
    }
};
