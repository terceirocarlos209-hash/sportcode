<?php
namespace Sportscore\Domain\Statistics;

interface StatisticsRepository
{
    public function findByMatchId(int $matchId): array;

    public function findByMatchIdAndTeamId(int $matchId, int $teamId): ?Statistics;

    public function save(Statistics $stats): bool;

    public function update(Statistics $stats): bool;

    public function delete(int $id): bool;

    public function deleteByMatchId(int $matchId): bool;
}
