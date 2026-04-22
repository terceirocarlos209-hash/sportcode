<?php
namespace Sportscore\Domain\Team;

class Team
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $badge,
        public readonly string $color,
        public readonly ?\DateTime $createdAt = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'slug'      => $this->slug,
            'badge'     => $this->badge,
            'color'     => $this->color,
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
