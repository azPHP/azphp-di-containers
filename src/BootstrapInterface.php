<?php


namespace AZphp\DI;


interface BootstrapInterface
{
    /**
     * Returns a DI container
     */
    public function boot();
    public function prepare(array $configs): void;
}
