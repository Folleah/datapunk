<?php declare(strict_types=1);

class DatabaseConfig
{
    private string $dataDir;

    public function __construct(string $dataDir)
    {
        $this->dataDir = $dataDir;
    }

    public function getDataDir(): string
    {
        return $this->dataDir;
    }
}