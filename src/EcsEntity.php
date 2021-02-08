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
        $entityData = $this->getOwner()->getEntityData($this->getId());
        $pool = $this->getOwner()->getComponentsPool($component::class);
        $componentIdx = $entityData->getComponent($component::class);
        if ($componentIdx !== null) {
            $pool->set($componentIdx, $component);
        } else {
            $entityData->addComponent($component::class, $pool->add($component));
            $this->getOwner()->updateFilters($this->getId(), $entityData);
        }

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }
}