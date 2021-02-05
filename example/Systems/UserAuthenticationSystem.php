<?php declare(strict_types=1);

use Invariance\Ecs\EcsContext;
use Invariance\Ecs\System\EcsExecuteSystem;

class UserAuthenticationSystem implements EcsExecuteSystem
{
    public EcsContext $context;

    public function execute(): void
    {
        $filteredEntities = $this->context->filter([DatabaseConnectionComponent::class, UserComponent::class]);
        foreach ($filteredEntities as $entity) {
            /** @var UserComponent $userComponent */
            $userComponent = $entity->getComponent(UserComponent::class);
            if ($userComponent === null) {
                return;
            }
        }
    }
}