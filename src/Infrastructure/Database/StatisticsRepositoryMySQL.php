<?php
namespace Sportscore\Infrastructure\Database;

use Sportscore\Domain\Statistics\Statistics;
use Sportscore\Domain\Statistics\StatisticsRepository;

class StatisticsRepositoryMySQL implements StatisticsRepository
{
    private string $table;

    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix . 'ss_statistics';
    }

    public function findByMatchId(int $matchId): array
    {
        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$this->table} WHERE match_id = %d", $matchId)
        );

        return array_map(fn($row) => $this->hydrate($row), $rows ?? []);
    }

    public function findByMatchIdAndTeamId(int $matchId, int $teamId): ?Statistics
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE match_id = %d AND team_id = %d",
                $matchId,
                $teamId
            )
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function save(Statistics $stats): bool
    {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table,
            [
                'match_id'      => $stats->matchId,
                'team_id'       => $stats->teamId,
                'possession'    => $stats->possession,
                'shots'         => $stats->shots,
                'shots_on_target' => $stats->shotsOnTarget,
                'passes'        => $stats->passes,
                'pass_accuracy' => $stats->passAccuracy,
                'corners'       => $stats->corners,
                'fouls'         => $stats->fouls,
                'offsides'      => $stats->offsides,
                'updated_at'    => current_time('mysql'),
            ],
            ['%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s']
        );

        return $result !== false;
    }

    public function update(Statistics $stats): bool
    {
        global $wpdb;

        $result = $wpdb->update(
            $this->table,
            [
                'possession'    => $stats->possession,
                'shots'         => $stats->shots,
                'shots_on_target' => $stats->shotsOnTarget,
                'passes'        => $stats->passes,
                'pass_accuracy' => $stats->passAccuracy,
                'corners'       => $stats->corners,
                'fouls'         => $stats->fouls,
                'offsides'      => $stats->offsides,
                'updated_at'    => current_time('mysql'),
            ],
            ['id' => $stats->id],
            ['%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s'],
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

    public function deleteByMatchId(int $matchId): bool
    {
        global $wpdb;

        $result = $wpdb->delete(
            $this->table,
            ['match_id' => $matchId],
            ['%d']
        );

        return $result !== false;
    }

    private function hydrate(\stdClass $row): Statistics
    {
        return new Statistics(
            id: (int)$row->id,
            matchId: (int)$row->match_id,
            teamId: (int)$row->team_id,
            possession: (int)$row->possession,
            shots: (int)$row->shots,
            shotsOnTarget: (int)$row->shots_on_target,
            passes: (int)$row->passes,
            passAccuracy: (int)$row->pass_accuracy,
            corners: (int)$row->corners,
            fouls: (int)$row->fouls,
            offsides: (int)$row->offsides,
            updatedAt: $row->updated_at ? new \DateTime($row->updated_at) : null,
        );
    }
}
