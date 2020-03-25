<?php

/**
 * µ (Mu) is a joke of a micro-framework made by Jeremy.
 *
 * @see https://github.com/jeremeamia/mu
 */

namespace AZphp\DI\Mu;

use AZphp\DI\BootstrapInterface;

// Hi, I'm µ!
class µ{function __invoke($k,$v=null){$c=&$this->$k;if($v===null)return$c=is_callable($c)?$c($this):$c;$c=$v;return $this;}}

class Bootstrap implements BootstrapInterface
{
    protected µ $container;

    public function __construct()
    {
        $this->container = new µ();
    }

    public function boot(): µ
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
