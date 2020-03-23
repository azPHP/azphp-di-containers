<?php


namespace AZphp\DI\League;


use AZphp\DI\BootstrapInterface;
use League\Container\Container;

class Bootstrap implements BootstrapInterface
{
    /**
     * @var Container
     */
    protected Container $container;

    public function __construct()
    {
        $this->container = new Container();
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
