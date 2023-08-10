<?php

namespace Gavin\GuestlineBattleships\Game;

use Gavin\GuestlineBattleships\Enums\CellStateEnum;
use Gavin\GuestlineBattleships\Enums\DirectionEnum;

class Game
{
    /** @var Ship[] $ships */
    public array $ships = [];

    private int $gridSize = 10;

    /** @var array<string, CellStateEnum> */
    public array $attacks = [];

    public function __construct(
        ?array $shipsJson = null,
        ?array $attacksData = null,
    )
    {
        if(is_array($shipsJson) && !empty($shipsJson)){
            foreach ($shipsJson as $shipData){
                $this->ships[] = $shipData['class']::fromArray($shipData);
            }
        }

        if(is_array($attacksData)){
            foreach ($attacksData as $key => $attackEnumValue){
                $this->attacks[ $key ] = CellStateEnum::tryFrom($attackEnumValue);
            }
        }

        if(!$shipsJson && !$attacksData){
            $this->restart();
        }
    }

    public function restart(): void
    {
        $this->generateShip(Battleship::class);
        $this->generateShip(Destroyer::class);
        $this->generateShip(Destroyer::class);
    }

    public function attack(int $x, int $y): CellStateEnum
    {
        $this->attacks["$x:$y"] = CellStateEnum::MISS;

        // does this hit any ships?
        foreach($this->ships as $ship){
            $cellInfo = $ship->getSegment($x, $y);

            if($cellInfo === false){
                $ship->setSegment($x, $y, true);
                $this->attacks["$x:$y"] = CellStateEnum::HIT;

                break;
            }
        }

        return $this->attacks["$x:$y"];
    }

    public function shipstoArray(): array
    {
        $toReturn = [];
        foreach ($this->ships as $ship) {
            $toReturn[] = $ship->toArray();
        }

        return $toReturn;
    }

    public function attacksToArray(): array
    {
        $toReturn = [];
        foreach ($this->attacks as $key => $attack){
            $toReturn[$key] = $attack->value;
        }

        return $toReturn;
    }

    private function generateShip(string $shipClass): void
    {
        $failed = false;

        do{

            $ship = match ($shipClass){
                Destroyer::class => $this->getDestroyer(),
                Battleship::class => $this->getBattleship(),
            };

            // check not colliding with ships
            foreach ($this->ships as $otherShip){
                if($ship->collides($otherShip)){
                    $failed = true;
                    break;
                }
            }

            // check not leaving the grid
            foreach ($ship->getSegments() as $segment){
                if(
                    $segment['x'] > $this->gridSize || $segment['x'] < 0
                    || $segment['y'] > $this->gridSize || $segment['y'] < 0
                ){
                    $failed = true;
                    break;
                }
            }

            if(!$failed){
                $this->ships[] = $ship;
            }

        }while($failed);

    }

    private function getDestroyer(): Destroyer
    {
        $random = $this->getRandomConfig();

        return new Destroyer(
            $random['x'],
            $random['y'],
            $random['direction'],
        );
    }

    private function getBattleship(): Battleship
    {
        $random = $this->getRandomConfig();

        return new Battleship(
            $random['x'],
            $random['y'],
            $random['direction'],
        );
    }

    /**
     * @return array{x: int, y: int, direction: DirectionEnum}
     * @throws \Exception
     */
    private function getRandomConfig(): array
    {
        $allDirections = DirectionEnum::cases();

        do{
            $valid = true;

            // segments are generated TOWARDS the direction,
            // so the x and y should not be closer to the edge than the length of the ship
            $randomIndex = random_int(0, count($allDirections) - 1);
            $x = random_int(0, $this->gridSize-1);
            $y = random_int(0, $this->gridSize-1);

            $direction = $allDirections[$randomIndex];

            if($direction === DirectionEnum::NORTH){
                if($y - 5 < 0){
                    $valid = false;
                }
            }

            if($direction === DirectionEnum::SOUTH){
                if($y + 5 > $this->gridSize - 1){
                    $valid = false;
                }
            }

            if($direction === DirectionEnum::EAST){
                if($x + 5 > $this->gridSize - 1){
                    $valid = false;
                }
            }

            if($direction === DirectionEnum::WEST){
                if($x - 5 < 0){
                    $valid = false;
                }
            }

        }while(!$valid);

        return [
            'x' => $x,
            'y' => $y,
            'direction' => $allDirections[ $randomIndex ],
        ];
    }

    public function isGameOver(): bool
    {
        // the game is over if all ship segments are hit
        foreach ($this->ships as $ship){
            foreach ($ship->getSegments() as $segment){
                if($segment['hit'] === false){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return array<int, array<int, CellStateEnum>>
     */
    public function getGrid(): array
    {
        $toReturn = [];

        foreach (range(0, $this->gridSize-1) as $x){
            $toReturn[$x] ??= [];

            foreach (range(0, $this->gridSize-1) as $y){
                $enum = $this->attacks["$x:$y"] ?? CellStateEnum::UNKNOWN;
                $toReturn[$x][$y] = $enum->value;
            }
        }

        return $toReturn;
    }
}