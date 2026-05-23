<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('theme_primary', 7)->default('#b91c1c')->after('tagline');
            $table->string('theme_primary_dark', 7)->default('#991b1b')->after('theme_primary');
            $table->string('theme_hero_accent', 7)->default('#dceee9')->after('theme_primary_dark');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['theme_primary', 'theme_primary_dark', 'theme_hero_accent']);
        });
    }
};
