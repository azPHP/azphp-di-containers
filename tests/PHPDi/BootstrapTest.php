<?php

namespace AZphpTests\DI\PHPDi;

use AZphp\DI\Constants;
use AZphp\DI\PHPDi\Bootstrap;
use AZphp\DI\Thing;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;

use function DI\create;
use function DI\autowire;

class BootstrapTest extends TestCase
{

    public function testBoot()
    {
        $bootstrap = new Bootstrap();
        $configs = [
            // alias logger interface
            LoggerInterface::class => create(TestLogger::class),
            // specify api key for Thing
            Thing::class => autowire(Thing::class)
                ->constructorParameter('apiKey', Constants::THING_API_KEY),
        ];

        $bootstrap->prepare([$configs]);

        $di = $bootstrap->boot();

        /** @var Thing $thing */
        $thing = $di->make(Thing::class);
        $this->assertInstanceOf(Thing::class, $thing);
        $this->assertInstanceOf(TestLogger::class, $thing->logger);

        $thing->run();

        /** @var TestLogger $logger */
        $logger = $di->make(LoggerInterface::class);

        $this->assertTrue($logger->hasRecords('info'), 'Logger was not shared :(');
    }
}
