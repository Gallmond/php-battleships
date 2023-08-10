<?php

namespace Gavin\GuestlineBattleships\Api;

use Gavin\GuestlineBattleships\database\DataStoreInterface;
use Gavin\GuestlineBattleships\Game\Game;

readonly class Api
{

    public function __construct(
        private DataStoreInterface $store
    )
    {
    }

    public function handleRequest(): never
    {
        $body = $this->getBody();

        match ($body['action']){
            'debug' => $this->handleDebug(),
            'new-game' => $this->handleNewGame(),
            'attack' => $this->handleAttack(),
            default => throw new \Exception("unhandled request")
        };
    }

    private function handleDebug(): never
    {
        $body = $this->getBody();

        $game = $this->getGame($body['game-id']);

        $this->respond([
            'ships' => $game->shipstoArray(),
            'attacks' => $game->attacksToArray(),
            'game-id' => $body['game-id']
        ]);
    }

    private function handleAttack(): never
    {
        $body = $this->getBody();

        $game = $this->getGame($body['game-id'] ?? null);

        $x = $body['x'];
        $y = $body['y'];

        $game->attack($x, $y);

        $this->saveGame($game, $body['game-id']);

        $this->respond([
            'game-id' => $body['game-id'],
            'attacks' => $game->attacksToArray(),
            'game-over' => $game->isGameOver(),
        ]);
    }

    private function handleNewGame(): never
    {
        $body = $this->getBody();

        $game = $this->getGame($body['game-id'] ?? null);

        $this->respond([
            'game-id' => $body['game-id'],
            'attacks' => $game->attacksToArray()
        ]);
    }

    private function saveGame(Game $game, string $gameId): void
    {
        $this->store->putMany([
            "$gameId:attacks" => $game->attacksToArray(),
            "$gameId:ships" => $game->shipstoArray(),
        ]);
    }

    public function getGame(
        ?string $gameId = null
    ): Game
    {
        $ships = $gameId !== null
            ? $this->store->get("$gameId:ships")
            : null;

        $attacks = $gameId !== null
            ? $this->store->get("$gameId:attacks")
            : null;

        $game = new Game($ships, $attacks);

        $this->saveGame($game, $gameId);

        return $game;
    }

    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function getBody(): array
    {
        $input = file_get_contents('php://input');

        return json_decode($input, true);
    }

    public function respond(array $data): never
    {
        header('content-type: application/json');
        echo json_encode($data);
        die();
    }
}