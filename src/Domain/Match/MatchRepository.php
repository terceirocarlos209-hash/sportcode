<?php
namespace Sportscore\Domain\Match;

interface MatchRepository
{
    /**
     * Find a match by its ID
     */
    public function findById(int $id): ?Match;

    /**
     * Find all live matches
     */
    public function findLive(): array;

    /**
     * Find matches scheduled for a given date
     */
    public function findByDate(\DateTime $date): array;

    /**
     * Find recent matches (finished)
     */
    public function findRecent(int $limit = 10): array;

    /**
     * Find next scheduled matches
     */
    public function findNext(int $limit = 10): array;

    /**
     * Find matches by team ID
     */
    public function findByTeamId(int $teamId): array;

    /**
     * Find matches by competition
     */
    public function findByCompetition(string $competition): array;

    /**
     * Save a match
     */
    public function save(Match $match): bool;

    /**
     * Update a match
     */
    public function update(Match $match): bool;

    /**
     * Delete a match
     */
    public function delete(int $id): bool;

    /**
     * Get total count of matches
     */
    public function count(): int;
}
