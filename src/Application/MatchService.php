<?php
namespace Sportscore\Application;

use Sportscore\Domain\Match\Match;
use Sportscore\Domain\Match\MatchRepository;
use Sportscore\Domain\Event\EventRepository;
use Sportscore\Domain\Statistics\StatisticsRepository;
use Sportscore\Shared\Exceptions\DomainException;

class MatchService
{
    public function __construct(
        private MatchRepository $matchRepository,
        private EventRepository $eventRepository,
        private StatisticsRepository $statisticsRepository,
    ) {}

    /**
     * Get complete match data for Match Center
     */
    public function getMatchCenter(int $id): array
    {
        $match = $this->matchRepository->findById($id);

        if (!$match) {
            throw DomainException::notFound('Match', $id);
        }

        $events = $this->eventRepository->findByMatchIdOrderedByMinute($id);
        $statistics = $this->statisticsRepository->findByMatchId($id);

        return [
            'match'       => $match->toArray(),
            'events'      => array_map(fn($event) => $event->toArray(), $events),
            'statistics'  => array_map(fn($stat) => $stat->toArray(), $statistics),
            'isLive'      => $match->isLive(),
            'isFinished'  => $match->isFinished(),
            'winner'      => $match->getWinner(),
            'isDraw'      => $match->isDraw(),
        ];
    }

    /**
     * Get all live matches with timeline
     */
    public function getLiveMatches(): array
    {
        $liveMatches = $this->matchRepository->findLive();

        return array_map(function ($match) {
            $events = $this->eventRepository->findByMatchIdOrderedByMinute($match->id);

            return [
                'match'  => $match->toArray(),
                'events' => array_map(fn($event) => $event->toArray(), $events),
                'isLive' => true,
            ];
        }, $liveMatches);
    }

    /**
     * Get recent finished matches
     */
    public function getRecentMatches(int $limit = 10): array
    {
        return array_map(
            fn($match) => $match->toArray(),
            $this->matchRepository->findRecent($limit)
        );
    }

    /**
     * Get next scheduled matches
     */
    public function getNextMatches(int $limit = 10): array
    {
        return array_map(
            fn($match) => $match->toArray(),
            $this->matchRepository->findNext($limit)
        );
    }

    /**
     * Get matches for a specific date
     */
    public function getMatchesByDate(\DateTime $date): array
    {
        return array_map(
            fn($match) => $match->toArray(),
            $this->matchRepository->findByDate($date)
        );
    }

    /**
     * Get team's match history
     */
    public function getTeamMatches(int $teamId): array
    {
        return array_map(
            fn($match) => $match->toArray(),
            $this->matchRepository->findByTeamId($teamId)
        );
    }

    /**
     * Get matches by competition
     */
    public function getCompetitionMatches(string $competition): array
    {
        return array_map(
            fn($match) => $match->toArray(),
            $this->matchRepository->findByCompetition($competition)
        );
    }

    /**
     * Update match score and status
     */
    public function updateMatchScore(int $id, int $homeScore, int $awayScore, string $status): array
    {
        $match = $this->matchRepository->findById($id);

        if (!$match) {
            throw DomainException::notFound('Match', $id);
        }

        $match->updateScore($homeScore, $awayScore);
        $match->updateStatus($status);

        if (!$this->matchRepository->update($match)) {
            throw new \Exception('Failed to update match score.');
        }

        return $match->toArray();
    }

    /**
     * Get match statistics by team
     */
    public function getMatchStatistics(int $matchId, int $teamId): ?array
    {
        $stats = $this->statisticsRepository->findByMatchIdAndTeamId($matchId, $teamId);

        return $stats ? $stats->toArray() : null;
    }

    /**
     * Get all events for a match (timeline)
     */
    public function getMatchTimeline(int $matchId): array
    {
        $events = $this->eventRepository->findByMatchIdOrderedByMinute($matchId);

        return array_map(fn($event) => $event->toArray(), $events);
    }

    /**
     * Get scorers for a match
     */
    public function getMatchScorers(int $matchId): array
    {
        $goals = $this->eventRepository->findGoalsByMatchId($matchId);

        return array_map(fn($goal) => $goal->toArray(), $goals);
    }

    /**
     * Get total matches count
     */
    public function getTotalMatches(): int
    {
        return $this->matchRepository->count();
    }
}
