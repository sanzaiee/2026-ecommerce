<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('position');
            $table->index(['is_featured', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
