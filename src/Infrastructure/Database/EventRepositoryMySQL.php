<?php
namespace Sportscore\Infrastructure\Database;

use Sportscore\Domain\Event\Event;
use Sportscore\Domain\Event\EventRepository;

class EventRepositoryMySQL implements EventRepository
{
    private string $table;

    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix . 'ss_events';
    }

    public function findById(int $id): ?Event
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id)
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findByMatchId(int $matchId): array
    {
        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$this->table} WHERE match_id = %d", $matchId)
        );

        return array_map(fn($row) => $this->hydrate($row), $rows ?? []);
    }

    public function findByMatchIdOrderedByMinute(int $matchId): array
    {
        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE match_id = %d ORDER BY minute ASC",
                $matchId
            )
        );

        return array_map(fn($row) => $this->hydrate($row), $rows ?? []);
    }

    public function findGoalsByMatchId(int $matchId): array
    {
        global $wpdb;

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table}
                 WHERE match_id = %d AND type IN ('goal', 'own goal', 'normal goal')
                 ORDER BY minute ASC",
                $matchId
            )
        );

        return array_map(fn($row) => $this->hydrate($row), $rows ?? []);
    }

    public function save(Event $event): bool
    {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table,
            [
                'match_id'    => $event->matchId,
                'team_id'     => $event->teamId,
                'minute'      => $event->minute,
                'type'        => $event->type,
                'player_id'   => $event->playerId,
                'description' => $event->description,
                'created_at'  => current_time('mysql'),
            ],
            ['%d', '%d', '%d', '%s', '%d', '%s', '%s']
        );

        return $result !== false;
    }

    public function update(Event $event): bool
    {
        global $wpdb;

        $result = $wpdb->update(
            $this->table,
            [
                'minute'      => $event->minute,
                'type'        => $event->type,
                'description' => $event->description,
            ],
            ['id' => $event->id],
            ['%d', '%s', '%s'],
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

    public function count(): int
    {
        global $wpdb;

        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");

        return (int)$count;
    }

    private function hydrate(\stdClass $row): Event
    {
        return new Event(
            id: (int)$row->id,
            matchId: (int)$row->match_id,
            teamId: (int)$row->team_id,
            minute: (int)$row->minute,
            type: $row->type,
            playerId: $row->player_id ? (int)$row->player_id : null,
            description: $row->description,
            createdAt: $row->created_at ? new \DateTime($row->created_at) : null,
        );
    }
}
