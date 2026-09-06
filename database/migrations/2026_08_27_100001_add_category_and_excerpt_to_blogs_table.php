<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->foreignId('blog_category_id')
                ->nullable()
                ->after('slug')
                ->constrained('blog_categories')
                ->nullOnDelete();
            $table->string('excerpt', 500)->nullable()->after('content');

            $table->index('blog_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('blog_category_id');
            $table->dropColumn('excerpt');
        });
    }
};
