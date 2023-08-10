<?php

namespace Gavin\GuestlineBattleships\Game;

use Gavin\GuestlineBattleships\Enums\DirectionEnum;

class Battleship extends Ship
{
    public function __construct(
        int $x, int $y, DirectionEnum $directionEnum
    )
    {
        $type = 'battleship';
        $length = 5;
        parent::__construct($type, $x, $y, $length, $directionEnum);
    }

    public function toArray(): array
    {
        return [
            'class' => $this::class,
            'x' => $this->x,
            'y' => $this->y,
            'direction' => $this->directionEnum->value,
            'segments' => $this->getSegments()
        ];
    }

    public static function fromArray(array $data): self
    {
        $x = $data['x'];
        $y = $data['y'];
        $direction = $data['direction'];
        $segments = $data['segments'];

        $inst = new self($x, $y, DirectionEnum::tryFrom($direction));
        foreach ($segments as $segment){
            $inst->setSegment(
                $segment['x'],
                $segment['y'],
                $segment['hit'],
            );
        }

        return $inst;
    }
}