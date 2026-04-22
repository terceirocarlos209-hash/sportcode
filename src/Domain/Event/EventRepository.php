<?php
namespace Sportscore\Domain\Event;

interface EventRepository
{
    public function findById(int $id): ?Event;

    public function findByMatchId(int $matchId): array;

    public function findByMatchIdOrderedByMinute(int $matchId): array;

    public function findGoalsByMatchId(int $matchId): array;

    public function save(Event $event): bool;

    public function update(Event $event): bool;

    public function delete(int $id): bool;

    public function deleteByMatchId(int $matchId): bool;

    public function count(): int;
}
