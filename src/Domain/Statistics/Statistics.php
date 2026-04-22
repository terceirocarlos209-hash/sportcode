<?php
namespace Sportscore\Domain\Statistics;

class Statistics
{
    public function __construct(
        public readonly int $id,
        public readonly int $matchId,
        public readonly int $teamId,
        public readonly int $possession,
        public readonly int $shots,
        public readonly int $shotsOnTarget,
        public readonly int $passes,
        public readonly int $passAccuracy,
        public readonly int $corners,
        public readonly int $fouls,
        public readonly int $offsides,
        public readonly ?\DateTime $updatedAt = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'matchId'        => $this->matchId,
            'teamId'         => $this->teamId,
            'possession'     => $this->possession,
            'shots'          => $this->shots,
            'shotsOnTarget'  => $this->shotsOnTarget,
            'passes'         => $this->passes,
            'passAccuracy'   => $this->passAccuracy,
            'corners'        => $this->corners,
            'fouls'          => $this->fouls,
            'offsides'       => $this->offsides,
            'updatedAt'      => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
