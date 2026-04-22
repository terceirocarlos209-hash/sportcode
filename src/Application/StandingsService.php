<?php
namespace Sportscore\Application;

use Sportscore\Domain\Match\MatchRepository;
use Sportscore\Domain\Team\TeamRepository;

class StandingsService
{
    public function __construct(
        private MatchRepository $matchRepository,
        private TeamRepository $teamRepository,
    ) {}

    /**
     * Generate standings for a competition
     */
    public function getCompetitionStandings(string $competition): array
    {
        $teams = $this->teamRepository->findAll();
        $matches = $this->matchRepository->findByCompetition($competition);

        $standings = [];

        foreach ($teams as $team) {
            $stats = $this->calculateTeamStats($team->id, $matches);
            $standings[] = [
                'team'     => $team->toArray(),
                'position' => 0,
                'stats'    => $stats,
            ];
        }

        usort($standings, fn($a, $b) => $this->compareTeams($a['stats'], $b['stats']));

        foreach ($standings as $key => &$entry) {
            $entry['position'] = $key + 1;
        }

        return $standings;
    }

    /**
     * Get all competitions with standings
     */
    public function getAllCompetitionsStandings(): array
    {
        $matches = $this->matchRepository->findRecent(1000);

        $competitions = [];

        foreach ($matches as $match) {
            if (!isset($competitions[$match->competition])) {
                $competitions[$match->competition] = [];
            }
            $competitions[$match->competition][] = $match;
        }

        return array_map(
            fn($name) => [
                'competition' => $name,
                'standings'   => $this->getCompetitionStandings($name),
            ],
            array_keys($competitions)
        );
    }

    /**
     * Calculate statistics for a team
     */
    private function calculateTeamStats(int $teamId, array $matches): array
    {
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

        return [
            'played'        => $played,
            'wins'          => $wins,
            'draws'         => $draws,
            'losses'        => $losses,
            'goalsFor'      => $goalsFor,
            'goalsAgainst'  => $goalsAgainst,
            'goalDifference' => $goalsFor - $goalsAgainst,
            'points'        => $wins * 3 + $draws,
        ];
    }

    /**
     * Compare two teams for sorting in standings
     */
    private function compareTeams(array $statsA, array $statsB): int
    {
        if ($statsA['points'] !== $statsB['points']) {
            return $statsB['points'] - $statsA['points'];
        }

        if ($statsA['goalDifference'] !== $statsB['goalDifference']) {
            return $statsB['goalDifference'] - $statsA['goalDifference'];
        }

        return $statsB['goalsFor'] - $statsA['goalsFor'];
    }
}
