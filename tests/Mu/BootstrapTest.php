<?php

namespace AZphpTests\DI\Mu;

use AZphp\DI\Constants;
use AZphp\DI\EntityManager;
use AZphp\DI\Mu\Bootstrap;
use AZphp\DI\Thing;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class BootstrapTest extends TestCase
{
    public function testBoot()
    {
        $bootstrap = new Bootstrap();

        $configs = [
            // alias logger interface
            static function ($µ) {
                return $µ(LoggerInterface::class, new Logger('foo', [
                    new TestHandler(),
                ]));
            },
            static function ($µ) {
                return $µ(EntityManager::class, new EntityManager());
            },
            // specify api key for Thing
            static function ($µ) {
                return $µ(Thing::class, function ($µ) {
                    return new Thing($µ(LoggerInterface::class), Constants::THING_API_KEY, $µ(EntityManager::class));
                });
            },
        ];

        $bootstrap->prepare($configs);

        $µ = $bootstrap->boot();

        /** @var Thing $thing */
        $thing = $µ(Thing::class);
        $this->assertInstanceOf(Thing::class, $thing);
        $this->assertInstanceOf(Logger::class, $thing->logger);

        $thing->run();

        /** @var Logger $logger */
        $logger = $µ(LoggerInterface::class);
        /** @var TestHandler $handler */
        $handler = $logger->getHandlers()[0];

        $this->assertTrue($handler->hasRecords('info'), 'Logger was not shared :(');
    }
}
