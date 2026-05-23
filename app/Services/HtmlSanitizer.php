<?php

namespace App\Services;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><h2><h3><h4><ul><ol><li><a><blockquote><table><thead><tbody><tr><th><td>';

    public function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $clean = strip_tags($html, self::ALLOWED_TAGS);
        $clean = preg_replace('/(<a\s[^>]*href=["\'])(javascript:[^"\']*)(["\'])/i', '$1#$3', $clean) ?? $clean;
        $clean = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $clean) ?? $clean;

        return trim($clean) !== '' ? $clean : null;
    }
}
