<?php
namespace Sportscore;

use Sportscore\Infrastructure\Http\Routes;

class Bootstrap
{
    /**
     * Initialize the architecture
     */
    public static function init(): void
    {
        Routes::register();
    }
}
