<?php declare(strict_types=1);

namespace Invariance\Ecs;

class EcsContext
{
    /** @var \SplFixedArray|EcsEntity[] */
    private \SplFixedArray $entityPool;
    /** @var \SplFixedArray|array[] */
    private \SplFixedArray $componentPool;

    public function __construct(?EcsConfig $config = null)
    {
        if ($config === null) {
            $config = new EcsConfig();
        }
        $this->entityPool = new \SplFixedArray($config->entityCacheSize);
        $this->componentPool = new \SplFixedArray($config->componentCacheSize);
    }

    public function makeEntity(): EcsEntity
    {
        $entity = new EcsEntity($this, count($this->entityPool) + 1);
        $this->entityPool[$entity->getId()] = $entity;

        return $entity;
    }

    /**
     * @param string[] $componentNames
     * @return EcsFilterResult
     */
    public function filter(array $componentNames): EcsFilterResult
    {
        $intersectedResult = [];
        $iteratedFirst = false;
        foreach ($componentNames as $name) {
            if (!$iteratedFirst) {
                $intersectedResult = $this->componentPool[$name];
                $iteratedFirst = true;
                continue;
            }
            $intersectedResult = array_intersect_key($intersectedResult, $this->componentPool[$name]);
        }

        $result = [];
        foreach ($componentNames as $name) {
            foreach ($intersectedResult as $id => $_) {
                $result[$id][] = $this->componentPool[$name][$id];
            }
        }

        $filterResult = new EcsFilterResult();
        foreach ($result as $id => $components) {
            $filteredEntity = new EcsFilteredEntity($this->entityPool[$id]);
            foreach ($components as $component) {
                $filteredEntity->addComponent($component);
            }
            $filterResult->add($filteredEntity);
        }

        return $filterResult;
    }

    public function replaceEntityComponent(int $id, EcsComponent $component): void
    {
        $this->componentPool[get_class($component)][$id] = $component;
    }
}