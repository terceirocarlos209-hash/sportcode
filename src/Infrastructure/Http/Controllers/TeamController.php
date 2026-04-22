<?php
namespace Sportscore\Infrastructure\Http\Controllers;

use Sportscore\Application\TeamService;
use Sportscore\Infrastructure\Database\{TeamRepositoryMySQL, MatchRepositoryMySQL};
use Sportscore\Shared\Exceptions\DomainException;
use WP_REST_Request;
use WP_REST_Response;

class TeamController
{
    /**
     * GET /wp-json/sportscore/v1/team/{id}
     * Get team profile with recent matches
     */
    public static function show(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $id = (int)$request->get_param('id');

            $service = new TeamService(
                new TeamRepositoryMySQL(),
                new MatchRepositoryMySQL(),
            );

            $data = $service->getTeamProfile($id);

            return new WP_REST_Response($data, 200);
        } catch (DomainException $e) {
            return new WP_REST_Response(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * GET /wp-json/sportscore/v1/team/slug/{slug}
     * Get team by slug
     */
    public static function showBySlug(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $slug = sanitize_text_field($request->get_param('slug'));

            $service = new TeamService(
                new TeamRepositoryMySQL(),
                new MatchRepositoryMySQL(),
            );

            $data = $service->getTeamBySlug($slug);

            return new WP_REST_Response($data, 200);
        } catch (DomainException $e) {
            return new WP_REST_Response(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * GET /wp-json/sportscore/v1/teams
     * Get all teams
     */
    public static function getAll(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $service = new TeamService(
                new TeamRepositoryMySQL(),
                new MatchRepositoryMySQL(),
            );

            $data = $service->getAllTeams();

            return new WP_REST_Response($data, 200);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * GET /wp-json/sportscore/v1/team/{id}/statistics
     * Get team statistics
     */
    public static function getStatistics(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $id = (int)$request->get_param('id');

            $service = new TeamService(
                new TeamRepositoryMySQL(),
                new MatchRepositoryMySQL(),
            );

            $data = $service->getTeamStatistics($id);

            return new WP_REST_Response($data, 200);
        } catch (\Throwable $e) {
            return new WP_REST_Response(['error' => 'Internal server error'], 500);
        }
    }
}
