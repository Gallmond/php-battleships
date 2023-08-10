<?php

namespace Gavin\GuestlineBattleships\database;

interface DataStoreInterface
{
    public function put(string $key, mixed $val): void;
    public function putMany(array $keyVals): void;
    public function get(string $key): mixed;
    public function has(string $key): bool;

}