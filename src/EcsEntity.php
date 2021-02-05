<?php declare(strict_types=1);

namespace Invariance\Ecs;

class EcsEntity
{
    private int $id;
    private EcsContext $owner;

    public function __construct(EcsContext $owner, int $id)
    {
        $this->owner = $owner;
        $this->id = $id;
    }

    public function getOwner(): EcsContext
    {
        return $this->owner;
    }

    public function replace(EcsComponent $component): self
    {
        $this->owner->replaceEntityComponent($this->id, $component);
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }
}