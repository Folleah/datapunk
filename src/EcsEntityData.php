<?php declare(strict_types=1);

namespace Invariance\Ecs;

class EcsEntityData
{
    /** @var int[] */
    private array $components = [];

    public function addComponent(string $componentName, int $idx): void
    {
        $this->components[$componentName] = $idx;
    }

    public function getComponent(string $componentName): int|null
    {
        return $this->components[$componentName] ?? null;
    }

    public function getComponents(): array
    {
        return $this->components;
    }
}