<?php


namespace AZphp\DI\PHPDi;


use AZphp\DI\BootstrapInterface;
use DI\Container;
use DI\ContainerBuilder;

class Bootstrap implements BootstrapInterface
{
    /**
     * @var ContainerBuilder
     */
    protected ContainerBuilder $builder;

    public function __construct()
    {
        $this->builder = new ContainerBuilder();
    }

    public function boot(): Container
    {
        return $this->builder->build();
    }

    public function prepare(array $configs): void
    {
        foreach ($configs as $config) {
            $this->builder->addDefinitions($config);
        }
    }
}
