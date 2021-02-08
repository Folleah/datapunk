<?php declare(strict_types=1);

namespace Invariance\Ecs;

use Invariance\Ecs\Filter\EcsFilteredResult;
use Invariance\Ecs\Filter\EcsFilterIncluded;
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
        $this->injections[$object::class] = $object;
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
                $typeString = (string)$type;
                $systemProp->setAccessible(true);
                if ($type === null || $systemProp->isStatic() || ($systemProp->isInitialized($system) && $systemProp->getDefaultValue() !== null)) {
                    continue;
                }

                // inject context
                if ($typeString === EcsContext::class) {
                    $systemProp->setValue($system, $this->context);
                    continue;
                }

                if ($typeString === EcsFilterIncluded::class) {
                    throw new \Exception('Cant use EcsFilter type for dependency injection, use attribute instead.');
                }

                // inject filters
                $systemPropAttributes = $systemProp->getAttributes(EcsFilterIncluded::class);
                if (count($systemPropAttributes) > 0) {
                    if ($typeString !== EcsFilteredResult::class) {
                        throw new \Exception('Property with EcsFilterIncluded attribute must be a EcsFilteredResult type.');
                    }

                    $componentNames = [];
                    foreach ($systemPropAttributes as $attribute) {
                        $componentNames = $attribute->getArguments();
                    }

                    $systemProp->setValue($system, $this->context->filter($componentNames));
                    continue;
                }

                if (!array_key_exists($typeString, $this->injections)) {
                    if (!$type->allowsNull()) {
                        throw new \Exception(sprintf('Need inject type `%s` for system `%s`.', $typeString, $reflectSystem->getName()));
                    }
                    continue;
                }

                $systemProp->setValue($system, $this->injections[$typeString]);
            }
        }
        $this->isInjected = true;
    }
}