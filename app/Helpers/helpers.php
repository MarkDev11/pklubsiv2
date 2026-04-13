<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

if (! function_exists('encryptUrl')) {
    /**
     * Encrypt a value for use in URLs.
     */
    function encryptUrl(string|int $value): string
    {
        return Crypt::encryptString((string) $value);
    }
}

if (! function_exists('decryptUrl')) {
    /**
     * Decrypt a URL parameter. Aborts with 403 on failure.
     */
    function decryptUrl(string $encrypted): string
    {
        try {
            return Crypt::decryptString($encrypted);
        } catch (DecryptException $e) {
            abort(403, 'Parameter URL tidak valid.');
        }
    }
}
