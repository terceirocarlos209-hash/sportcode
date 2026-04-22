<?php
namespace Sportscore\Infrastructure\Database;

use Sportscore\Domain\Team\Team;
use Sportscore\Domain\Team\TeamRepository;

class TeamRepositoryMySQL implements TeamRepository
{
    private string $table;

    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix . 'ss_teams';
    }

    public function findById(int $id): ?Team
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id)
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findBySlug(string $slug): ?Team
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table} WHERE slug = %s", $slug)
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(): array
    {
        global $wpdb;

        $rows = $wpdb->get_results("SELECT * FROM {$this->table} ORDER BY name ASC");

        return array_map(fn($row) => $this->hydrate($row), $rows ?? []);
    }

    public function save(Team $team): bool
    {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table,
            [
                'name'       => $team->name,
                'slug'       => $team->slug,
                'badge'      => $team->badge,
                'color'      => $team->color,
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%s']
        );

        return $result !== false;
    }

    public function update(Team $team): bool
    {
        global $wpdb;

        $result = $wpdb->update(
            $this->table,
            [
                'name'   => $team->name,
                'badge'  => $team->badge,
                'color'  => $team->color,
            ],
            ['id' => $team->id],
            ['%s', '%s', '%s'],
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

    private function hydrate(\stdClass $row): Team
    {
        return new Team(
            id: (int)$row->id,
            name: $row->name,
            slug: $row->slug,
            badge: $row->badge,
            color: $row->color,
            createdAt: $row->created_at ? new \DateTime($row->created_at) : null,
        );
    }
}
