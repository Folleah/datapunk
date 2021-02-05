<?php declare(strict_types=1);

namespace Invariance\Ecs\System;

interface EcsInitSystem extends EcsSystem
{
    public function init(): void;
}