<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Validates an uploaded file is a real image by checking its magic bytes,
 * not just the declared MIME type or file extension.
 */
class ValidImage implements ValidationRule
{
    private const SIGNATURES = [
        'jpg'  => ["\xFF\xD8\xFF"],
        'png'  => ["\x89PNG\r\n\x1a\n"],
        'gif'  => ['GIF87a', 'GIF89a'],
        'webp' => ['RIFF'],
        'bmp'  => ['BM'],
        'heic' => ["\x00\x00\x00"],
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('The :attribute must be an uploaded file.');
            return;
        }

        $handle = fopen($value->getRealPath(), 'rb');
        if ($handle === false) {
            $fail('The :attribute could not be read.');
            return;
        }

        $header = fread($handle, 12);
        fclose($handle);

        foreach (self::SIGNATURES as $signatures) {
            foreach ($signatures as $sig) {
                if (str_starts_with($header, $sig)) {
                    return;
                }
            }
        }

        // WEBP special case: 'RIFF????WEBP'
        if (str_starts_with($header, 'RIFF') && substr($header, 8, 4) === 'WEBP') {
            return;
        }

        $fail('The :attribute must be a valid image file.');
    }
}
