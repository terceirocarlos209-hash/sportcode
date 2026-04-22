<?php
namespace Sportscore\Domain\Team;

interface TeamRepository
{
    public function findById(int $id): ?Team;

    public function findBySlug(string $slug): ?Team;

    public function findAll(): array;

    public function save(Team $team): bool;

    public function update(Team $team): bool;

    public function delete(int $id): bool;

    public function count(): int;
}
