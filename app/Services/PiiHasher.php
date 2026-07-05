<?php

namespace App\Services;

class PiiHasher
{
    public static function hash(string $value): string
    {
        $normalized = trim($value);

        // PBKDF2: deterministik (NIK sama = hasil sama), TAPI sengaja lambat dihitung
        // 100.000 iterasi -- makin tinggi makin lambat/aman, tapi juga makin berat di server
        return hash_pbkdf2(
            'sha256',
            $normalized,
            config('app.pii_hash_key'),
            100000,
            64
        );
    }
}
