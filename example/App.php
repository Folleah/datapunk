<?php declare(strict_types=1);

use Invariance\Ecs\EcsContext;
use Invariance\Ecs\EcsSystemsContainer;
use Invariance\Ecs\Systems\UserInitSystem;

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
        $this->systems->execute();
    }
}