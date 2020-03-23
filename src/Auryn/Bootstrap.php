<?php


namespace AZphp\DI\Auryn;

use Auryn\Injector;
use AZphp\DI\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    /**
     * @var Injector
     */
    protected Injector $container;

    public function __construct()
    {
        $this->container = new Injector();
    }

    public function boot(): Injector
    {
        return $this->container;
    }

    public function prepare(array $configs): void
    {
        foreach ($configs as $config) {
            $this->container = $config($this->container);
        }
    }
}
