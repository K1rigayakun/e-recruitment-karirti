<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->smallInteger('duration_minutes');
            $table->string('mode');
            $table->string('meeting_url')->nullable();
            $table->string('location')->nullable();
            $table->text('instructions')->nullable();
            $table->string('status')->default('scheduled');
            $table->text('applicant_note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('scheduled_at');
            $table->index(['application_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
