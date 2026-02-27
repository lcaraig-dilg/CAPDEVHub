<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_registrations', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('activity_id')
                ->constrained()
                ->cascadeOnDelete();

            // Optional link to a CAPDEVhub user (for logged-in registrations)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Snapshot of participant information at time of registration
            $table->string('first_name');
            $table->string('middle_initial', 1)->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('gender');
            $table->date('date_of_birth')->nullable();
            $table->unsignedTinyInteger('age')->nullable();

            $table->boolean('is_pwd')->default(false);
            $table->boolean('requires_assistance')->nullable();

            $table->string('office');
            $table->string('position');
            $table->string('lgu_organization');

            $table->string('contact_number', 50);
            $table->string('email');
            $table->string('dietary_restrictions', 500)->nullable();

            // Registration meta
            $table->enum('registration_type', ['user', 'guest'])->default('guest');

            $table->timestamps();

            // Prevent duplicate registrations for the same activity by email
            $table->unique(['activity_id', 'email'], 'activity_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_registrations');
    }
};

