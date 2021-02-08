<?php declare(strict_types=1);

namespace Invariance\Ecs\Example\Systems;

use Invariance\Ecs\EcsContext;
use Invariance\Ecs\Example\Components\DatabaseConnectionComponent;
use Invariance\Ecs\Example\Components\UserComponent;
use Invariance\Ecs\Filter\EcsFilterIncluded;
use Invariance\Ecs\Filter\EcsFilteredResult;
use Invariance\Ecs\System\EcsExecuteSystem;

class UserAuthenticationSystem implements EcsExecuteSystem
{
    private EcsContext $context;
    #[EcsFilterIncluded(DatabaseConnectionComponent::class, UserComponent::class)]
    private EcsFilteredResult $filteredEntities;

    public function execute(): void
    {
        /** @var EcsFilteredResult $entity */
        foreach ($this->filteredEntities as $i => $_) {
            /** @var UserComponent $userComponent */
            $userComponent = $this->filteredEntities->getFirst($i);
            $userComponent->email = 'test';
            $userComponent->name = 'test';
        }
    }
}