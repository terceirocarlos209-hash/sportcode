<?php
namespace Sportscore\Domain\Match;

use Sportscore\Shared\Exceptions\DomainException;

class Match
{
    private const VALID_STATUSES = ['scheduled', 'live', 'finished', 'postponed', 'cancelled'];

    public function __construct(
        public readonly int $id,
        public readonly int $homeTeamId,
        public readonly int $awayTeamId,
        public readonly \DateTime $date,
        public string $status,
        public int $homeScore,
        public int $awayScore,
        public readonly string $competition,
        public readonly ?\DateTime $createdAt = null,
        public readonly ?\DateTime $updatedAt = null,
    ) {
        $this->validateStatus($status);
        $this->validateScores($homeScore, $awayScore);
    }

    private function validateStatus(string $status): void
    {
        if (!in_array(strtolower($status), self::VALID_STATUSES, true)) {
            throw DomainException::invalidStatus($status);
        }
    }

    private function validateScores(int $home, int $away): void
    {
        if ($home < 0 || $away < 0) {
            throw DomainException::invalidScore($home < 0 ? $home : $away);
        }
    }

    public function isLive(): bool
    {
        return strtolower($this->status) === 'live';
    }

    public function isFinished(): bool
    {
        return strtolower($this->status) === 'finished';
    }

    public function updateScore(int $homeScore, int $awayScore): void
    {
        $this->validateScores($homeScore, $awayScore);
        $this->homeScore = $homeScore;
        $this->awayScore = $awayScore;
    }

    public function updateStatus(string $status): void
    {
        $this->validateStatus($status);
        $this->status = strtolower($status);
    }

    public function getWinner(): ?int
    {
        if (!$this->isFinished()) {
            return null;
        }

        if ($this->homeScore > $this->awayScore) {
            return $this->homeTeamId;
        }

        if ($this->awayScore > $this->homeScore) {
            return $this->awayTeamId;
        }

        return null;
    }

    public function isDraw(): bool
    {
        return $this->homeScore === $this->awayScore;
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'homeTeamId'   => $this->homeTeamId,
            'awayTeamId'   => $this->awayTeamId,
            'date'         => $this->date->format('Y-m-d H:i:s'),
            'status'       => $this->status,
            'homeScore'    => $this->homeScore,
            'awayScore'    => $this->awayScore,
            'competition'  => $this->competition,
            'createdAt'    => $this->createdAt?->format('Y-m-d H:i:s'),
            'updatedAt'    => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
