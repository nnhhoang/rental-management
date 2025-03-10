<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class LocaleHelper
{
    /**
     * Available application locales.
     *
     * @var array
     */
    protected static $locales = ['en', 'vi'];

    /**
     * Get all available locales.
     *
     * @return array
     */
    public static function getLocales()
    {
        return self::$locales;
    }

    /**
     * Check if locale is valid.
     *
     * @param string $locale
     * @return bool
     */
    public static function isValidLocale($locale)
    {
        return in_array($locale, self::$locales);
    }

    /**
     * Get current locale name.
     *
     * @return string
     */
    public static function getCurrentLocaleName()
    {
        $locale = App::getLocale();
        
        $names = [
            'en' => 'English',
            'vi' => 'Tiếng Việt',
        ];
        
        return $names[$locale] ?? $locale;
    }

    /**
     * Get current locale flag emoji.
     *
     * @return string
     */
    public static function getCurrentLocaleFlag()
    {
        $locale = App::getLocale();
        
        $flags = [
            'en' => '🇺🇸',
            'vi' => '🇻🇳',
        ];
        
        return $flags[$locale] ?? '';
    }
}