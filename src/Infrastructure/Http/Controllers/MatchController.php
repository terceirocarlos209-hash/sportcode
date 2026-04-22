<?php
namespace Sportscore\Infrastructure\Http\Controllers;

use Sportscore\Application\MatchService;
use Sportscore\Infrastructure\Database\{
    MatchRepositoryMySQL,
    EventRepositoryMySQL,
    StatisticsRepositoryMySQL
};
use Sportscore\Shared\Exceptions\DomainException;
use WP_REST_Request;
use WP_REST_Response;

class MatchController
{
    /**
     * GET /wp-json/sportscore/v1/match/{id}
     * Get complete match center data
     */
    public static function show(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $id = (int)$request->get_param('id');

            $service = new MatchService(
                new MatchRepositoryMySQL(),
                new EventRepositoryMySQL(),
                new StatisticsRepositoryMySQL(),
            );

            $data = $service->getMatchCenter($id);

            return new WP_REST_Response($data, 200);
        } catch (DomainException $e) {
            return new WP_REST_Response(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * GET /wp-json/sportscore/v1/matches/live
     * Get all live matches
     */
    public static function getLive(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $service = new MatchService(
                new MatchRepositoryMySQL(),
                new EventRepositoryMySQL(),
                new StatisticsRepositoryMySQL(),
            );

            $data = $service->getLiveMatches();

            return new WP_REST_Response($data, 200);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * GET /wp-json/sportscore/v1/matches/recent
     * Get recent finished matches
     */
    public static function getRecent(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $limit = min((int)$request->get_param('limit') ?? 10, 50);

            $service = new MatchService(
                new MatchRepositoryMySQL(),
                new EventRepositoryMySQL(),
                new StatisticsRepositoryMySQL(),
            );

            $data = $service->getRecentMatches($limit);

            return new WP_REST_Response($data, 200);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * GET /wp-json/sportscore/v1/matches/next
     * Get next scheduled matches
     */
    public static function getNext(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $limit = min((int)$request->get_param('limit') ?? 10, 50);

            $service = new MatchService(
                new MatchRepositoryMySQL(),
                new EventRepositoryMySQL(),
                new StatisticsRepositoryMySQL(),
            );

            $data = $service->getNextMatches($limit);

            return new WP_REST_Response($data, 200);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * GET /wp-json/sportscore/v1/match/{id}/timeline
     * Get match timeline (events)
     */
    public static function getTimeline(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $id = (int)$request->get_param('id');

            $service = new MatchService(
                new MatchRepositoryMySQL(),
                new EventRepositoryMySQL(),
                new StatisticsRepositoryMySQL(),
            );

            $data = $service->getMatchTimeline($id);

            return new WP_REST_Response($data, 200);
        } catch (DomainException $e) {
            return new WP_REST_Response(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * GET /wp-json/sportscore/v1/match/{id}/scorers
     * Get match scorers
     */
    public static function getScorers(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $id = (int)$request->get_param('id');

            $service = new MatchService(
                new MatchRepositoryMySQL(),
                new EventRepositoryMySQL(),
                new StatisticsRepositoryMySQL(),
            );

            $data = $service->getMatchScorers($id);

            return new WP_REST_Response($data, 200);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * PUT /wp-json/sportscore/v1/match/{id}
     * Update match score and status
     */
    public static function update(WP_REST_Request $request): WP_REST_Response
    {
        try {
            if (!current_user_can('manage_options')) {
                return new WP_REST_Response(['error' => 'Unauthorized'], 403);
            }

            $id = (int)$request->get_param('id');
            $homeScore = (int)$request->get_param('homeScore') ?? 0;
            $awayScore = (int)$request->get_param('awayScore') ?? 0;
            $status = $request->get_param('status') ?? 'live';

            $service = new MatchService(
                new MatchRepositoryMySQL(),
                new EventRepositoryMySQL(),
                new StatisticsRepositoryMySQL(),
            );

            $data = $service->updateMatchScore($id, $homeScore, $awayScore, $status);

            return new WP_REST_Response($data, 200);
        } catch (DomainException $e) {
            return new WP_REST_Response(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }
}
