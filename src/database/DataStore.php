<?php

namespace Gavin\GuestlineBattleships\database;

class DataStore implements DataStoreInterface
{
    private readonly string $filePath;
    private array $data = [];

    public function __construct()
    {
        $this->filePath = sys_get_temp_dir() . '/gavin-battleships.json';

        $this->load();
    }

    private function load(): void
    {
        $this->data = file_exists($this->filePath)
            ? json_decode(file_get_contents($this->filePath), true)
            : [];
    }

    private function save(): void
    {
        $json = json_encode($this->data);

        $success = file_put_contents($this->filePath, $json);
        if(!$success){
            throw new \Exception("Failed to save data");
        }
    }

    public function putMany(array $keyVals): void
    {
        foreach ($keyVals as $key => $val){
            $this->data[$key] = $val;
        }

        $this->save();
    }

    public function put(string $key, mixed $val): void
    {
        $this->data[$key] = $val;

        $this->save();
    }

    public function has(string $key): bool
    {
        $this->load();

        return isset($this->data[$key]);
    }

    public function get(string $key): mixed
    {
        $this->load();

        return $this->data[$key] ?? null;
    }
}