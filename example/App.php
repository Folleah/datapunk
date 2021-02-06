<?php declare(strict_types=1);

namespace Invariance\Ecs\Example;

use Invariance\Ecs\EcsContext;
use Invariance\Ecs\EcsSystemsContainer;
use Invariance\Ecs\Example\Systems\UserAuthenticationSystem;
use Invariance\Ecs\Example\Systems\UserInitSystem;

class App
{
    public EcsContext $context;
    public EcsSystemsContainer $systems;

    public function __construct()
    {
        $this->context = new EcsContext();
        $this->systems = new EcsSystemsContainer($this->context);
        $this->systems
            ->add(new UserInitSystem())
            ->add(new UserAuthenticationSystem())
            ->inject(new DatabaseConfig('db'))
        ;
        $this->systems->init();
    }

    public function index()
    {
        $i = 0;
        while ($i < 2) {
            $this->systems->execute();
            $i++;
        }
    }
}