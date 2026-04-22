<?php
namespace Sportscore\Application;

use Sportscore\Domain\Team\Team;
use Sportscore\Domain\Team\TeamRepository;
use Sportscore\Domain\Match\MatchRepository;
use Sportscore\Shared\Exceptions\DomainException;

class TeamService
{
    public function __construct(
        private TeamRepository $teamRepository,
        private MatchRepository $matchRepository,
    ) {}

    /**
     * Get team details with recent matches
     */
    public function getTeamProfile(int $id): array
    {
        $team = $this->teamRepository->findById($id);

        if (!$team) {
            throw DomainException::notFound('Team', $id);
        }

        $matches = $this->matchRepository->findByTeamId($id);

        return [
            'team'    => $team->toArray(),
            'matches' => array_map(fn($match) => $match->toArray(), array_slice($matches, 0, 10)),
            'totalMatches' => count($matches),
        ];
    }

    /**
     * Get team by slug
     */
    public function getTeamBySlug(string $slug): array
    {
        $team = $this->teamRepository->findBySlug($slug);

        if (!$team) {
            throw DomainException::notFound('Team', $slug);
        }

        return $team->toArray();
    }

    /**
     * Get all teams
     */
    public function getAllTeams(): array
    {
        return array_map(
            fn($team) => $team->toArray(),
            $this->teamRepository->findAll()
        );
    }

    /**
     * Get team statistics
     */
    public function getTeamStatistics(int $teamId): array
    {
        $matches = $this->matchRepository->findByTeamId($teamId);

        $wins = 0;
        $draws = 0;
        $losses = 0;
        $goalsFor = 0;
        $goalsAgainst = 0;

        foreach ($matches as $match) {
            if (!$match->isFinished()) {
                continue;
            }

            $isHome = $match->homeTeamId === $teamId;

            if ($isHome) {
                $goalsFor += $match->homeScore;
                $goalsAgainst += $match->awayScore;

                if ($match->homeScore > $match->awayScore) {
                    $wins++;
                } elseif ($match->homeScore === $match->awayScore) {
                    $draws++;
                } else {
                    $losses++;
                }
            } else {
                $goalsFor += $match->awayScore;
                $goalsAgainst += $match->homeScore;

                if ($match->awayScore > $match->homeScore) {
                    $wins++;
                } elseif ($match->awayScore === $match->homeScore) {
                    $draws++;
                } else {
                    $losses++;
                }
            }
        }

        $played = $wins + $draws + $losses;
        $points = $wins * 3 + $draws * 1;

        return [
            'teamId'       => $teamId,
            'played'       => $played,
            'wins'         => $wins,
            'draws'        => $draws,
            'losses'       => $losses,
            'goalsFor'     => $goalsFor,
            'goalsAgainst' => $goalsAgainst,
            'goalDifference' => $goalsFor - $goalsAgainst,
            'points'       => $points,
            'winRate'      => $played > 0 ? round(($wins / $played) * 100, 2) : 0,
        ];
    }
}
