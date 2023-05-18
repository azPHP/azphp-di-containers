<?php

namespace AZphpTests\DI\Symfony;

use AZphp\DI\Constants;
use AZphp\DI\Symfony\Bootstrap;
use AZphp\DI\Thing;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class BootstrapTest extends TestCase
{

    public function testBoot()
    {
        $bootstrap = new Bootstrap();

        $configs = [
            // alias logger interface
            static function (ContainerBuilder $container) {
                $container->autowire(LoggerInterface::class, Logger::class)
                    ->setArgument('name', 'mylogger')
                    ->setArgument('handlers', [
                        $container->get(TestHandler::class),
                    ]);
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
        $this->assertInstanceOf(Logger::class, $thing->logger);

        $thing->run();

        /** @var Logger $logger */
        $logger = $di->get(LoggerInterface::class);
        /** @var TestHandler $handler */
        $handler = $logger->getHandlers()[0];

        $this->assertTrue($handler->hasRecords('info'), 'Logger was not shared :(');
    }
}
