<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            $table->string('hero_eyebrow')->nullable();
            $table->string('hero_lead')->nullable();
            $table->string('story_heading')->nullable();
            $table->text('story_paragraph_1')->nullable();
            $table->text('story_paragraph_2')->nullable();
            $table->string('gallery_heading')->nullable();
            $table->string('gallery_lead')->nullable();
            $table->string('process_heading')->nullable();
            $table->json('values')->nullable();
            $table->json('process_steps')->nullable();
            $table->longText('editorial_body')->nullable();
            $table->string('cta_title')->nullable();
            $table->text('cta_text')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();
        });

        $aboutContent = null;
        if (Schema::hasColumn('site_settings', 'about_content')) {
            $aboutContent = DB::table('site_settings')->value('about_content');
        }

        DB::table('about_pages')->insert([
            'id' => 1,
            'hero_eyebrow' => 'Our story',
            'hero_lead' => null,
            'story_heading' => 'From Nepal to your table',
            'story_paragraph_1' => 'Mandira Foods brings premium dried fruits and traditional Nepali pickles to homes across the country. We partner with growers who share our respect for the land, then slow-dry, season, and pack each batch so flavor stays honest from the first bite to the last.',
            'story_paragraph_2' => 'Whether you are filling a pantry, packing a lunchbox, or sending a gift, we want every product to feel as thoughtful as something you would share with family.',
            'gallery_heading' => 'Crafted with care',
            'gallery_lead' => 'A glimpse of what we make — simple ingredients, proud Nepali roots.',
            'process_heading' => 'How we work',
            'values' => json_encode([
                ['icon' => 'bi-flower1', 'title' => '100% Natural', 'text' => 'No artificial preservatives, colors, or flavors in our core range.'],
                ['icon' => 'bi-droplet-half', 'title' => 'No Added Sugar', 'text' => 'Sun-ripened fruit, slow-dried to keep natural sweetness.'],
                ['icon' => 'bi-geo-alt', 'title' => 'Rooted in Nepal', 'text' => 'Trusted growers and time-honored methods from farm to pack.'],
                ['icon' => 'bi-box-seam', 'title' => 'Packed with Care', 'text' => 'Resealable pouches and careful handling for lasting freshness.'],
            ]),
            'process_steps' => json_encode([
                ['title' => 'Select', 'text' => 'Hand-picked fruit and spices from partners we know and trust.'],
                ['title' => 'Prepare', 'text' => 'Slow drying and traditional recipes — no shortcuts on taste.'],
                ['title' => 'Pack', 'text' => 'Sealed for freshness and delivered across Nepal with care.'],
            ]),
            'editorial_body' => $aboutContent,
            'cta_title' => 'Taste the difference',
            'cta_text' => 'Explore our dried fruits, pickles, and gift-ready packs — shipped across Nepal.',
            'meta_title' => 'About Us — Mandira Foods',
            'meta_description' => 'Learn about Mandira Foods — premium dried fruits and traditional pickles crafted in Nepal.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('about_pages');
    }
};
