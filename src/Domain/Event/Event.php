<?php
namespace Sportscore\Domain\Event;

class Event
{
    private const VALID_TYPES = [
        'goal', 'own goal', 'penalty', 'missed penalty',
        'yellow card', 'red card', 'yellowred card',
        'substitution', 'var', 'injury time'
    ];

    public function __construct(
        public readonly int $id,
        public readonly int $matchId,
        public readonly int $teamId,
        public readonly int $minute,
        public readonly string $type,
        public readonly ?int $playerId = null,
        public readonly ?string $description = null,
        public readonly ?\DateTime $createdAt = null,
    ) {}

    public function getIconEmoji(): string
    {
        return match (strtolower($this->type)) {
            'goal', 'normal goal'     => '⚽',
            'own goal'                => '🥅',
            'penalty'                 => '🎯',
            'missed penalty'          => '❌',
            'yellow card'             => '🟨',
            'red card'                => '🟥',
            'yellowred card'          => '🟧',
            'substitution'            => '🔄',
            'var'                     => '📺',
            'injury time'             => '⏱️',
            default                   => '•',
        };
    }

    public function isGoal(): bool
    {
        return in_array(strtolower($this->type), ['goal', 'own goal', 'normal goal'], true);
    }

    public function isCard(): bool
    {
        return str_contains(strtolower($this->type), 'card');
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'matchId'     => $this->matchId,
            'teamId'      => $this->teamId,
            'minute'      => $this->minute,
            'type'        => $this->type,
            'playerId'    => $this->playerId,
            'description' => $this->description,
            'icon'        => $this->getIconEmoji(),
            'createdAt'   => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
