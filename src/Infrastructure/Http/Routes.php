<?php
namespace Sportscore\Infrastructure\Http;

use Sportscore\Infrastructure\Http\Controllers\{MatchController, TeamController, StandingsController};

class Routes
{
    public static function register(): void
    {
        add_action('rest_api_init', [self::class, 'registerMatchRoutes']);
        add_action('rest_api_init', [self::class, 'registerTeamRoutes']);
        add_action('rest_api_init', [self::class, 'registerStandingsRoutes']);
    }

    public static function registerMatchRoutes(): void
    {
        register_rest_route('sportscore/v1', '/match/(?P<id>\d+)', [
            'methods'  => 'GET',
            'callback' => [MatchController::class, 'show'],
            'args'     => [
                'id' => [
                    'type'     => 'integer',
                    'required' => true,
                ],
            ],
        ]);

        register_rest_route('sportscore/v1', '/matches/live', [
            'methods'  => 'GET',
            'callback' => [MatchController::class, 'getLive'],
        ]);

        register_rest_route('sportscore/v1', '/matches/recent', [
            'methods'  => 'GET',
            'callback' => [MatchController::class, 'getRecent'],
            'args'     => [
                'limit' => [
                    'type'    => 'integer',
                    'default' => 10,
                ],
            ],
        ]);

        register_rest_route('sportscore/v1', '/matches/next', [
            'methods'  => 'GET',
            'callback' => [MatchController::class, 'getNext'],
            'args'     => [
                'limit' => [
                    'type'    => 'integer',
                    'default' => 10,
                ],
            ],
        ]);

        register_rest_route('sportscore/v1', '/match/(?P<id>\d+)/timeline', [
            'methods'  => 'GET',
            'callback' => [MatchController::class, 'getTimeline'],
            'args'     => [
                'id' => [
                    'type'     => 'integer',
                    'required' => true,
                ],
            ],
        ]);

        register_rest_route('sportscore/v1', '/match/(?P<id>\d+)/scorers', [
            'methods'  => 'GET',
            'callback' => [MatchController::class, 'getScorers'],
            'args'     => [
                'id' => [
                    'type'     => 'integer',
                    'required' => true,
                ],
            ],
        ]);

        register_rest_route('sportscore/v1', '/match/(?P<id>\d+)', [
            'methods'  => 'PUT',
            'callback' => [MatchController::class, 'update'],
            'args'     => [
                'id'        => [
                    'type'     => 'integer',
                    'required' => true,
                ],
                'homeScore' => [
                    'type' => 'integer',
                ],
                'awayScore' => [
                    'type' => 'integer',
                ],
                'status' => [
                    'type' => 'string',
                ],
            ],
        ]);
    }

    public static function registerTeamRoutes(): void
    {
        register_rest_route('sportscore/v1', '/team/(?P<id>\d+)', [
            'methods'  => 'GET',
            'callback' => [TeamController::class, 'show'],
            'args'     => [
                'id' => [
                    'type'     => 'integer',
                    'required' => true,
                ],
            ],
        ]);

        register_rest_route('sportscore/v1', '/team/slug/(?P<slug>[a-z0-9-]+)', [
            'methods'  => 'GET',
            'callback' => [TeamController::class, 'showBySlug'],
            'args'     => [
                'slug' => [
                    'type'     => 'string',
                    'required' => true,
                ],
            ],
        ]);

        register_rest_route('sportscore/v1', '/teams', [
            'methods'  => 'GET',
            'callback' => [TeamController::class, 'getAll'],
        ]);

        register_rest_route('sportscore/v1', '/team/(?P<id>\d+)/statistics', [
            'methods'  => 'GET',
            'callback' => [TeamController::class, 'getStatistics'],
            'args'     => [
                'id' => [
                    'type'     => 'integer',
                    'required' => true,
                ],
            ],
        ]);
    }

    public static function registerStandingsRoutes(): void
    {
        register_rest_route('sportscore/v1', '/standings/(?P<competition>[a-z0-9-]+)', [
            'methods'  => 'GET',
            'callback' => [StandingsController::class, 'show'],
            'args'     => [
                'competition' => [
                    'type'     => 'string',
                    'required' => true,
                ],
            ],
        ]);

        register_rest_route('sportscore/v1', '/standings', [
            'methods'  => 'GET',
            'callback' => [StandingsController::class, 'getAll'],
        ]);
    }
}
