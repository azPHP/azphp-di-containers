<?php

namespace AZphp\DI\Mu;

use AZphp\DI\BootstrapInterface;
use Closure;

class Bootstrap implements BootstrapInterface
{
    protected Closure $container;

    public function __construct()
    {
        $c = new class(){function __invoke($k,$v=null){$c=&$this->$k;if($v===null)return$c=is_callable($c)?$c($this):$c;$c=$v;return $this;}};
        $this->container = Closure::fromCallable($c);
    }

    public function boot(): Closure
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
