<?php declare(strict_types=1);

namespace Invariance\Ecs;

class EcsFilterResult implements \Iterator, \Countable
{
    private int $position;
    /** @var EcsFilteredEntity[] */
    private array $entities;

    public function add(EcsFilteredEntity $entity): void
    {
        $this->entities[] = $entity;
    }

    public function current(): EcsFilteredEntity
    {
        return $this->entities[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return array_key_exists($this->position, $this->entities);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->entities);
    }
}