<?php declare(strict_types=1);

use Invariance\Ecs\EcsComponent;

class UserComponent implements EcsComponent
{
    public string $name;
    public string $email;
}