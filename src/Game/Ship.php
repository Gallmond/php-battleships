<?php

namespace Gavin\GuestlineBattleships\Game;

use Gavin\GuestlineBattleships\Enums\DirectionEnum;

abstract class Ship
{
    private array $segments = [];

    public function __construct(
        public string $type,
        public readonly int $x,
        public readonly int $y,
        public readonly int $length,
        public readonly DirectionEnum $directionEnum,
    )
    {
        $this->initSegments();
    }

    private function initSegments(): void
    {
        foreach (range(0, $this->length - 1) as $i){

            $segmentX = $this->x;
            $segmentY = $this->y;

            if($this->directionEnum === DirectionEnum::NORTH){
                $segmentY -= $i;
            }
            if($this->directionEnum === DirectionEnum::SOUTH){
                $segmentY += $i;
            }
            if($this->directionEnum === DirectionEnum::EAST){
                $segmentX += $i;
            }
            if($this->directionEnum === DirectionEnum::WEST){
                $segmentX -= $i;
            }

            $this->setSegment($segmentX, $segmentY, false);
        }
    }

    public function setSegment(int $x, int $y, bool $hit = false): void
    {
        if(!isset($this->segments[$x])){
            $this->segments[$x] = [];
        }

        $this->segments[$x][$y] = $hit;
    }

    /**
     * @param int $x
     * @param int $y
     * @return bool|null
     */
    public function getSegment(int $x, int $y): ?bool
    {
        return $this->segments[$x][$y] ?? null;
    }

    /**
     * @return array<int, array{x: int, y: int, hit: bool}>
     */
    public function getSegments(): array
    {
        $toReturn = [];

        foreach ($this->segments as $x => $_){
            foreach ($this->segments[$x] as $y => $isHit){
                $toReturn[] = [
                    'x' => $x,
                    'y' => $y,
                    'hit' => $isHit,
                ];
            }
        }

        return $toReturn;
    }

    public function collides(Ship $otherShip): bool
    {
        foreach ($otherShip->getSegments() as $otherShipSegment){
            if($this->getSegment($otherShipSegment['x'], $otherShipSegment['y']) !== null){
                return true;
            }
        }

        return false;
    }
}