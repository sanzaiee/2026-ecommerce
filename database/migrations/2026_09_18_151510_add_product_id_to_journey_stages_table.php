<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('journey_stages', 'product_id')) {
            return;
        }

        // Existing global rows are invalid once stages belong to a product.
        DB::table('journey_stages')->delete();

        Schema::table('journey_stages', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('journey_stages', 'product_id')) {
            return;
        }

        Schema::table('journey_stages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
        });
    }
};
