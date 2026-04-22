<?php
namespace Sportscore\Infrastructure\Http\Controllers;

use Sportscore\Application\StandingsService;
use Sportscore\Infrastructure\Database\{MatchRepositoryMySQL, TeamRepositoryMySQL};
use WP_REST_Request;
use WP_REST_Response;

class StandingsController
{
    /**
     * GET /wp-json/sportscore/v1/standings/{competition}
     * Get standings for a specific competition
     */
    public static function show(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $competition = sanitize_text_field($request->get_param('competition'));

            $service = new StandingsService(
                new MatchRepositoryMySQL(),
                new TeamRepositoryMySQL(),
            );

            $data = $service->getCompetitionStandings($competition);

            return new WP_REST_Response($data, 200);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * GET /wp-json/sportscore/v1/standings
     * Get all competitions standings
     */
    public static function getAll(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $service = new StandingsService(
                new MatchRepositoryMySQL(),
                new TeamRepositoryMySQL(),
            );

            $data = $service->getAllCompetitionsStandings();

            return new WP_REST_Response($data, 200);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }
}
