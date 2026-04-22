<?php
namespace Sportscore\Infrastructure\Database;

use Sportscore\Domain\Match\Match;
use Sportscore\Domain\Match\MatchRepository;
use Sportscore\Shared\Exceptions\DomainException;

class MatchRepositoryMySQL implements MatchRepository
{
    private string $table;

    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix . 'ss_matches';
    }

    public function findById(int $id): ?Match
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id)
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findLive(): array
    {
        global $wpdb;

        $rows = $wpdb->get_results(
            "SELECT * FROM {$this->table} WHERE status = 'live' ORDER BY match_date ASC"
        );

        return array_map(fn($row) => $this->hydrate($row), $rows ?? []);
    }

    public function findByDate(\DateTime $date): array
    {
        global $wpdb;

        $dateStr = $date->format('Y-m-d');

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE DATE(match_date) = %s ORDER BY match_date ASC",
                $dateStr
            )
        );

        return array_map(fn($row) => $this->hydrate($row), $rows ?? []);
    }

    public function findRecent(int $limit = 10): array
    {
        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table}
                 WHERE status = 'finished'
                 ORDER BY match_date DESC
                 LIMIT %d",
                $limit
            )
        );

        return array_map(fn($row) => $this->hydrate($row), $rows ?? []);
    }

    public function findNext(int $limit = 10): array
    {
        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table}
                 WHERE status IN ('scheduled', 'postponed')
                 ORDER BY match_date ASC
                 LIMIT %d",
                $limit
            )
        );

        return array_map(fn($row) => $this->hydrate($row), $rows ?? []);
    }

    public function findByTeamId(int $teamId): array
    {
        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table}
                 WHERE home_team_id = %d OR away_team_id = %d
                 ORDER BY match_date DESC",
                $teamId,
                $teamId
            )
        );

        return array_map(fn($row) => $this->hydrate($row), $rows ?? []);
    }

    public function findByCompetition(string $competition): array
    {
        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE competition = %s ORDER BY match_date DESC",
                $competition
            )
        );

        return array_map(fn($row) => $this->hydrate($row), $rows ?? []);
    }

    public function save(Match $match): bool
    {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table,
            [
                'home_team_id'  => $match->homeTeamId,
                'away_team_id'  => $match->awayTeamId,
                'match_date'    => $match->date->format('Y-m-d H:i:s'),
                'status'        => $match->status,
                'home_score'    => $match->homeScore,
                'away_score'    => $match->awayScore,
                'competition'   => $match->competition,
                'created_at'    => current_time('mysql'),
                'updated_at'    => current_time('mysql'),
            ],
            ['%d', '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%s']
        );

        return $result !== false;
    }

    public function update(Match $match): bool
    {
        global $wpdb;

        $result = $wpdb->update(
            $this->table,
            [
                'status'     => $match->status,
                'home_score' => $match->homeScore,
                'away_score' => $match->awayScore,
                'updated_at' => current_time('mysql'),
            ],
            ['id' => $match->id],
            ['%s', '%d', '%d', '%s'],
            ['%d']
        );

        return $result !== false;
    }

    public function delete(int $id): bool
    {
        global $wpdb;

        $result = $wpdb->delete(
            $this->table,
            ['id' => $id],
            ['%d']
        );

        return $result !== false;
    }

    public function count(): int
    {
        global $wpdb;

        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");

        return (int)$count;
    }

    private function hydrate(\stdClass $row): Match
    {
        return new Match(
            id: (int)$row->id,
            homeTeamId: (int)$row->home_team_id,
            awayTeamId: (int)$row->away_team_id,
            date: new \DateTime($row->match_date),
            status: $row->status,
            homeScore: (int)$row->home_score,
            awayScore: (int)$row->away_score,
            competition: $row->competition,
            createdAt: $row->created_at ? new \DateTime($row->created_at) : null,
            updatedAt: $row->updated_at ? new \DateTime($row->updated_at) : null,
        );
    }
}
