<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('promo_primary')->nullable()->after('tagline');
            $table->string('promo_secondary')->nullable()->after('promo_primary');
        });

        DB::table('site_settings')->where('id', 1)->update([
            'promo_primary' => 'Free shipping on orders above Rs. 2,000',
            'promo_secondary' => '100% natural products',
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['promo_primary', 'promo_secondary']);
        });
    }
};
