<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
        });

        Schema::create('applicant_skill', function (Blueprint $table) {
            $table->foreignId('applicant_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->primary(['applicant_profile_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_skill');
        Schema::dropIfExists('skills');
    }
};
