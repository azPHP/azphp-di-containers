<?php

namespace AZphpTests\DI\Symfony;

use AZphp\DI\Constants;
use AZphp\DI\Symfony\Bootstrap;
use AZphp\DI\Thing;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class BootstrapTest extends TestCase
{

    public function testBoot()
    {
        $bootstrap = new Bootstrap();

        $configs = [
            // alias logger interface
            static function (ContainerBuilder $container) {
                $container->setAlias(LoggerInterface::class, TestLogger::class);
            },
            // specify api key for Thing
            static function (ContainerBuilder $container) {
                $container->autowire(Thing::class, Thing::class)
                    // Using arbitrary strings for everything is silly
                    // this took a long time to find in the docs
                    ->setArgument('apiKey', Constants::THING_API_KEY);
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
