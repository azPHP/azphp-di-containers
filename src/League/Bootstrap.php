<?php


namespace AZphp\DI\League;


use AZphp\DI\BootstrapInterface;
use League\Container\Container;
use League\Container\ReflectionContainer;

class Bootstrap implements BootstrapInterface
{
    /**
     * @var Container
     */
    protected Container $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->container->delegate(new ReflectionContainer());
    }

    public function boot(): Container
    {
        return $this->container;
    }

    public function prepare(array $configs): void
    {
        foreach ($configs as $config) {
            $config($this->container);
        }
    }
}
