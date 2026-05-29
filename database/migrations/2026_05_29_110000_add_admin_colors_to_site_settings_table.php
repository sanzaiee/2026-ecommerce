<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('admin_color_primary', 7)->default('#3D2914')->after('admin_theme');
            $table->string('admin_color_secondary', 7)->default('#C9A227')->after('admin_color_primary');
            $table->string('admin_color_neutral', 7)->default('#7A6B5C')->after('admin_color_secondary');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['admin_color_primary', 'admin_color_secondary', 'admin_color_neutral']);
        });
    }
};
