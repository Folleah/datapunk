<?php declare(strict_types=1);

namespace Invariance\Ecs;

class EcsComponentsPool
{
    private int $componentsCount = 0;
    private \SplFixedArray $components;

    public function __construct(int $componentsPoolCacheSize)
    {
        $this->components = new \SplFixedArray($componentsPoolCacheSize);
    }

    public function add(EcsComponent $component): int
    {
        if (count($this->components) === $this->componentsCount) {
            $this->components->setSize($this->componentsCount << 1);
        }
        $idx = $this->componentsCount++;
        $this->components[$idx] = $component;

        return $idx;
    }

    public function get(int $idx): EcsComponent
    {
        if ($idx < 0 || $idx >= $this->componentsCount) {
            throw new \Exception('Invalid pool component id.');
        }
        return $this->components[$idx];
    }

    public function set(int $idx, EcsComponent $component): void
    {
        if ($idx < 0 || $idx > $this->componentsCount) {
            throw new \Exception('Invalid pool component id.');
        }

        $this->components[$idx] = $component;
    }
}