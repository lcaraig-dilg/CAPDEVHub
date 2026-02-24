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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('id');
            $table->string('middle_initial', 1)->nullable()->after('first_name');
            $table->string('last_name')->after('middle_initial');
            $table->string('suffix', 10)->nullable()->after('last_name');
            $table->enum('gender', ['Male', 'Female', 'Prefer not to say'])->after('suffix');
            $table->date('date_of_birth')->after('gender');
            $table->integer('age')->after('date_of_birth');
            $table->boolean('is_pwd')->default(false)->after('age');
            $table->boolean('requires_assistance')->nullable()->after('is_pwd');
            $table->string('office')->after('requires_assistance');
            $table->string('position')->after('office');
            $table->string('lgu_organization')->after('position');
            $table->string('contact_number', 20)->after('lgu_organization');
            $table->text('dietary_restrictions')->nullable()->after('contact_number');
            $table->enum('role', ['user', 'admin', 'super_admin'])->default('user')->after('dietary_restrictions');
            $table->string('username')->unique()->nullable()->after('email');
        });
        
        // Modify name field separately to avoid issues
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'middle_initial',
                'last_name',
                'suffix',
                'gender',
                'date_of_birth',
                'age',
                'is_pwd',
                'requires_assistance',
                'office',
                'position',
                'lgu_organization',
                'contact_number',
                'dietary_restrictions',
                'role',
                'username',
            ]);
            $table->string('name')->nullable(false)->change();
        });
    }
};
