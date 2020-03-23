<?php

namespace AZphpTests\DI\League;

use AZphp\DI\Constants;
use AZphp\DI\League\Bootstrap;
use AZphp\DI\Thing;
use League\Container\Container;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;


class BootstrapTest extends TestCase
{

    public function testBoot()
    {
        $bootstrap = new Bootstrap();
        // note that the configs we pass here are all callback methods that work
        // on the container directly...very easy to mess up
        $configs = [
            // alias logger interface
            static function (Container $container) {
                $container->add(LoggerInterface::class, TestLogger::class, true);
            },
            // specify api key for Thing
            static function (Container $container) {
                $container->add(Thing::class)
                    ->addArgument(LoggerInterface::class) // Why should we have to do this? It should be taken care of by autowiring!
                    ->addArgument(Constants::THING_API_KEY)
                    // we have a third constructor argument, but it's a class so autowiring should take care of it, right?
                ;
            },
        ];

        $bootstrap->prepare($configs);

        $di = $bootstrap->boot();

        /** @var Thing $thing */
        $thing = $di->get(Thing::class);
        $this->assertInstanceOf(Thing::class, $thing);
        $this->assertInstanceOf(TestLogger::class, $thing->logger);

        $thing->run();

        /** @var TestLogger $logger */
        $logger = $di->get(LoggerInterface::class);

        $this->assertTrue($logger->hasRecords('info'), 'Logger was not shared :(');
    }
}
