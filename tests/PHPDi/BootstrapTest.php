<?php

namespace AZphpTests\DI\PHPDi;

use AZphp\DI\Constants;
use AZphp\DI\PHPDi\Bootstrap;
use AZphp\DI\Thing;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use function DI\autowire;

class BootstrapTest extends TestCase
{

    public function testBoot()
    {
        $bootstrap = new Bootstrap();
        $configs = [
            // alias logger interface
            LoggerInterface::class => autowire(Logger::class)
                ->constructorParameter('name', 'mylogger')
                ->constructorParameter('handlers', [
                    new TestHandler(),
                ]),
            // specify api key for Thing
            Thing::class => autowire(Thing::class)
                // this is not even remotely possible, even the docs for php-di say so
                ->constructorParameter('logger', LoggerInterface::class)
                ->constructorParameter('apiKey', Constants::THING_API_KEY),
        ];

        $bootstrap->prepare([$configs]);

        $di = $bootstrap->boot();

        /** @var Thing $thing */
        $thing = $di->make(Thing::class);
        $this->assertInstanceOf(Thing::class, $thing);
        $this->assertInstanceOf(Logger::class, $thing->logger);

        $thing->run();

        /** @var Logger $logger */
        $logger = $di->make(LoggerInterface::class);
        /** @var TestHandler $handler */
        $handler = $logger->getHandlers()[0];

        $this->assertTrue($handler->hasRecords('info'), 'Logger was not shared :(');
    }
}
