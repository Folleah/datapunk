<?php declare(strict_types=1);

namespace Invariance\Ecs\Filter;

use Invariance\Ecs\EcsComponent;
use JetBrains\PhpStorm\Pure;

class EcsFilteredResult implements \Iterator, \Countable
{
    private int $position = 0;
    private array $components = [];

    public function add(int $entityId, EcsComponent $component): void
    {
        $this->components[$entityId][] = $component;
    }

    public function getFirst(int $idx): EcsComponent|null
    {
        return $this->components[$idx][0] ?? null;
    }

    public function refresh(): void
    {
        $this->components = [];
        $this->rewind();
    }

    public function getSecond(int $idx): EcsComponent|null
    {
        return $this->components[$idx][1] ?? null;
    }

    public function getThird(int $idx): EcsComponent|null
    {
        return $this->components[$idx][2] ?? null;
    }

    public function getFour(int $idx): EcsComponent|null
    {
        return $this->components[$idx][3] ?? null;
    }

    public function current(): array
    {
        return $this->components[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    #[Pure]
    public function valid(): bool
    {
        return array_key_exists($this->position, $this->components);
    }

    #[Pure]
    public function count(): int
    {
        return count($this->components);
    }
}