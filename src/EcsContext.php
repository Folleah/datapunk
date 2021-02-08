<?php declare(strict_types=1);

namespace Invariance\Ecs;

use Invariance\Ecs\Filter\EcsFilteredResult;

class EcsContext
{
    private EcsConfig $config;

    /** @var \SplFixedArray|EcsEntityData[] */
    private \SplFixedArray $entitiesPool;
    private int $entitiesCount = 0;
    private \SplStack $freeEntities;

    /** @var EcsComponentsPool[] */
    private array $componentsPools;

    /** @var EcsFilteredResult[] */
    private array $cachedFilters = [];

    public function __construct(EcsConfig $config = null)
    {
        if ($config === null) {
            $config = new EcsConfig();
        }
        $this->config = $config;
        $this->entitiesPool = new \SplFixedArray($config->entitiesCacheSize);
        $this->componentsPools = [];
        $this->freeEntities = new \SplStack();
    }

    /**
     * Make new Entity
     */
    public function makeEntity(): EcsEntity
    {
        $freeEntity = !$this->freeEntities->isEmpty()
            ? $this->freeEntities->pop()
            : null;
        if ($freeEntity !== null) {
            $idx = $freeEntity;
        } else {
            $entityPoolSize = count($this->entitiesPool);
            // realloc's
            if ($this->entitiesCount === $entityPoolSize) {
                $this->entitiesPool->setSize($this->entitiesCount << 1);
            }
            $idx = $this->entitiesCount++;
            $this->entitiesPool[$idx] = new EcsEntityData();
        }

        return new EcsEntity($this, $idx);
    }

    public function getEntityData(int $idx): EcsEntityData
    {
        if ($idx < 0 || $idx >= $this->entitiesCount) {
            throw new \Exception('Invalid entity id.');
        }
        return $this->entitiesPool[$idx];
    }

    public function getComponentsPool(string $componentName): EcsComponentsPool
    {
        if (isset($this->componentsPools[$componentName])) {
            return $this->componentsPools[$componentName];
        }

        if (!class_exists($componentName)) {
            throw new \Exception('Invalid component classname.');
        }

        $this->componentsPools[$componentName] = $pool = new EcsComponentsPool($this->config->componentsPoolCacheSize);

        return $pool;
    }

    /**
     * (FOR INTERNAL USE ONLY) Get filtered entities with attached components
     *
     * @param string[] $componentNames
     * @return EcsFilteredResult
     */
    public function filter(array $componentNames): EcsFilteredResult
    {
        $filterName = $this->getFilterName($componentNames);
        return $this->getFilter($filterName);
    }

    public function updateFilters(int $entityIdx, EcsEntityData $entityData): void
    {
        $filterName = $this->getFilterName(array_keys($entityData->getComponents()));
        $filter = $this->getFilter($filterName);
        foreach ($entityData->getComponents() as $cName => $cIdx) {
            $filter->add($entityIdx, $cIdx, $this->componentsPools[$cName]->get($cIdx));
        }
    }

    private function getFilter(string $filterName): EcsFilteredResult
    {
        if (!array_key_exists($filterName, $this->cachedFilters)) {
            $filter = new EcsFilteredResult();
            $this->cachedFilters[$filterName] = $filter;
        } else {
            $filter = $this->cachedFilters[$filterName];
        }

        return $filter;
    }

    private function getFilterName(array $componentNames): string
    {
        sort($componentNames, SORT_ASC | SORT_STRING);

        return implode('-', $componentNames);
    }
}