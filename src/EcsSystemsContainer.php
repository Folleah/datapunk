<?php declare(strict_types=1);

namespace Invariance\Ecs;

use Invariance\Ecs\System\EcsExecuteSystem;
use Invariance\Ecs\System\EcsInitSystem;
use Invariance\Ecs\System\EcsSystem;

final class EcsSystemsContainer
{
    private EcsContext $context;
    /** @var EcsSystem[] */
    private array $systems;
    /** @var object[] */
    private array $injections;
    private bool $isInitialized = false;
    private bool $isInjected = false;

    public function __construct(EcsContext $context)
    {
        $this->context = $context;
    }

    public function add(EcsSystem $system): self
    {
        $this->systems[] = $system;
        return $this;
    }

    public function inject(object $object): self
    {
        $this->injections[gettype($object)] = $object;
        return $this;
    }

    public function init(): void
    {
        if ($this->isInitialized) {
            throw new \Exception("Systems already initialized.");
        }

        $this->processInjects();

        foreach ($this->systems as $system) {
            if ($system instanceof EcsInitSystem) {
                $system->init();
            }
        }

        $this->isInitialized = true;
    }

    public function execute(): void
    {
        if (!$this->isInitialized) {
            throw new \Exception("EcsSystems should be initialized before.");
        }

        foreach ($this->systems as $system) {
            if ($system instanceof EcsExecuteSystem) {
                $system->execute();
            }
        }
    }

    private function processInjects(): void
    {
        if ($this->isInitialized) {
            throw new \Exception("Cant inject after systems initialized.");
        }

        if ($this->isInjected) {
            return;
        }

        foreach ($this->systems as $system) {
            $reflectSystem = new \ReflectionClass($system);
            foreach ($reflectSystem->getProperties() as $systemProp) {
                $type = $systemProp->getType();
                if ($type === null) {
                    continue;
                }
                $type = (string)$type;

                if ($type === EcsContext::class) {
                    $systemProp->setValue($this->context);
                    continue;
                }

                if (!array_key_exists($type, $this->injections)) {
                    continue;
                }

                $systemProp->setValue($this->injections[$type]);
            }
        }
        $this->isInjected = true;
    }
}