<?php


namespace AZphp\DI\Symfony;


use AZphp\DI\BootstrapInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Bootstrap implements BootstrapInterface
{
    protected ContainerBuilder $container;

    public function __construct()
    {
        $this->container = new ContainerBuilder();
    }

    public function boot(): ContainerBuilder
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
