<?php declare(strict_types=1);

namespace Invariance\Ecs;

class EcsFilteredEntity
{
    private EcsEntity $entity;
    /** @var EcsComponent[] */
    private array $components;

    public function __construct(EcsEntity $entity)
    {
        $this->entity = $entity;
    }

    public function addComponent(EcsComponent $component): void
    {
        $this->components[get_class($component)] = $component;
    }

    public function getEntity(): EcsEntity
    {
        return $this->entity;
    }

    public function getComponent(string $componentName): ?EcsComponent
    {
        return $this->components[$componentName] ?? null;
    }
}