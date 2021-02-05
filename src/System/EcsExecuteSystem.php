<?php declare(strict_types=1);

namespace Invariance\Ecs\System;

interface EcsExecuteSystem extends EcsSystem
{
    public function execute(): void;
}