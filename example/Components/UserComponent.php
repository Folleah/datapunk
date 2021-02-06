<?php declare(strict_types=1);

namespace Invariance\Ecs\Example\Components;

use Invariance\Ecs\EcsComponent;

class UserComponent implements EcsComponent
{
    public string $name;
    public string $email;
}