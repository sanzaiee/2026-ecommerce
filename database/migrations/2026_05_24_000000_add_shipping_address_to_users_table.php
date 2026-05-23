<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 32)->nullable()->after('email');
            $table->string('shipping_address_line1')->nullable()->after('phone');
            $table->string('shipping_address_line2')->nullable()->after('shipping_address_line1');
            $table->string('shipping_city', 120)->nullable()->after('shipping_address_line2');
            $table->string('shipping_district', 120)->nullable()->after('shipping_city');
            $table->string('shipping_postal_code', 16)->nullable()->after('shipping_district');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'shipping_address_line1',
                'shipping_address_line2',
                'shipping_city',
                'shipping_district',
                'shipping_postal_code',
            ]);
        });
    }
};
