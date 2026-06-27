<?php

namespace App\Support;

use Illuminate\Support\Str;

final class EmailNormalizer
{
    private const CYRILLIC_LOOKALIKES = [
        'А' => 'a',
        'а' => 'a',
        'В' => 'b',
        'Е' => 'e',
        'е' => 'e',
        'К' => 'k',
        'к' => 'k',
        'М' => 'm',
        'Н' => 'h',
        'О' => 'o',
        'о' => 'o',
        'Р' => 'p',
        'р' => 'p',
        'С' => 'c',
        'с' => 'c',
        'Т' => 't',
        'Х' => 'x',
        'х' => 'x',
        'У' => 'y',
        'у' => 'y',
    ];

    public static function normalize(mixed $email): string
    {
        $email = strtr((string) $email, self::CYRILLIC_LOOKALIKES);
        $email = preg_replace('/\s+/u', '', $email) ?? '';

        return Str::lower($email);
    }
}
