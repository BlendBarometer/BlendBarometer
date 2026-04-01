<?php

namespace App\Support;

class Whitespace
{
    public static function replaceAll(string $subject, string $replacement): string
    {
        return preg_replace('/[\p{Z}\s]/u', $replacement, $subject) ?? $subject;
    }
}
