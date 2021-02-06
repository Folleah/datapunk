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
     * @throws \Exception
     */
    public function filter(array $componentNames): EcsFilteredResult
    {
        $filteredResult = new EcsFilteredResult();
        $filterName = $this->getFilterName($componentNames);
        if (!array_key_exists($filterName, $this->cachedFilters)) {
            return $filteredResult;
        }

        foreach ($this->cachedFilters[$filterName] as $entityIdx => $componentIdxs) {
            foreach ($componentIdxs as $componentName => $componentIdx) {
                $filteredResult->add($entityIdx, $this->componentsPools[$componentName]->get($componentIdx));
            }
        }

        return $filteredResult;
    }

    // very slowly, need optimization
    public function updateFilters(): void
    {
        $this->cachedFilters = [];
        for ($eIdx = 0; $eIdx < $this->entitiesCount; $eIdx++) {
            $cNames = [];
            $cIdxs = [];
            foreach ($this->entitiesPool[$eIdx]->getComponents() as $cName => $cIdx) {
                $cNames[] = $cName;
                $cIdxs[$cName] = $cIdx;
            }

            if (count($cIdxs) > 0) {
                $this->cachedFilters[$this->getFilterName($cNames)][$eIdx] = $cIdxs;
            }
        }
    }

    private function getFilterName(array $componentNames): string
    {
        sort($componentNames, SORT_ASC | SORT_STRING);

        return implode('-', $componentNames);
    }
}