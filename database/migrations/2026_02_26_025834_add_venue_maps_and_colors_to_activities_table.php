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
        Schema::table('activities', function (Blueprint $table) {
            $table->text('venue_google_maps_link')->nullable()->after('venue');
            $table->string('accent_color_1')->nullable()->after('description');
            $table->string('accent_color_2')->nullable()->after('accent_color_1');
            $table->string('accent_color_3')->nullable()->after('accent_color_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['venue_google_maps_link', 'accent_color_1', 'accent_color_2', 'accent_color_3']);
        });
    }
};
