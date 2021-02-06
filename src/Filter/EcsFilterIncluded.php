<?php declare(strict_types=1);

namespace Invariance\Ecs\Filter;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EcsFilterIncluded
{
    public function __construct(...$params) {}
}