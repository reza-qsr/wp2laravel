<?php

namespace RezaQsr\Wp2Laravel\Support;

use Illuminate\Support\Str;

class Sanitizer
{
    /**
     * Sanitize a text field by trimming, removing extra spaces, stripping tags (with optional allowed tags), and escaping HTML.
     *
     * @param string $str The string to sanitize.
     * @param string $allowedTags Optional allowed HTML tags (e.g., '<p><a><strong>').
     * @return string The sanitized string.
     */
    public static function text(string $str, string $allowedTags = ''): string
    {
        $str = trim($str);
        if ($allowedTags) {
            $str = strip_tags($str, $allowedTags);
        } else {
            $str = strip_tags($str);
        }
        return htmlspecialchars(preg_replace('/\s+/', ' ', $str));
    }

    /**
     * Sanitize an email address.
     *
     * @param string $email The email to sanitize.
     * @return string The sanitized email.
     */
    public static function email(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize a slug by first sanitizing as text and then applying Laravel's slug helper.
     *
     * @param string $str The string to slugify.
     * @return string The sanitized slug.
     */
    public static function slug(string $str): string
    {
        return Str::slug(self::text($str));
    }

    /**
     * Sanitize a value for meta or option storage. If it's a string, sanitize as text.
     *
     * @param mixed $value The value to sanitize.
     * @return mixed The sanitized value.
     */
    public static function value(mixed $value): mixed
    {
        if (is_string($value)) {
            return self::text($value);
        }
        return $value;
    }
}