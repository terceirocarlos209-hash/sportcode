<?php
namespace Sportscore;

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
    }

    public static function load(string $class): void
    {
        if (strpos($class, 'Sportscore\\') !== 0) {
            return;
        }

        $path = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen('Sportscore\\'))) . '.php';

        if (file_exists($path)) {
            require_once $path;
        }
    }
}
