<?php

namespace AZphpTests\DI\Mu;

use AZphp\DI\Constants;
use AZphp\DI\EntityManager;
use AZphp\DI\Mu\Bootstrap;
use AZphp\DI\Thing;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;

class BootstrapTest extends TestCase
{
    public function testBoot()
    {
        $bootstrap = new Bootstrap();

        $configs = [
            // alias logger interface
            static function ($c) {
                return $c(LoggerInterface::class, fn () => new TestLogger());
            },
            static function ($c) {
                return $c(EntityManager::class, fn () => new EntityManager());
            },
            // specify api key for Thing
            static function ($c) {
                return $c(
                    Thing::class,
                    fn ($c) => new Thing($c(LoggerInterface::class), Constants::THING_API_KEY, $c(EntityManager::class))
                );
            },
        ];

        $bootstrap->prepare($configs);

        $di = $bootstrap->boot();

        /** @var Thing $thing */
        $thing = $di(Thing::class);
        $this->assertInstanceOf(Thing::class, $thing);
        $this->assertInstanceOf(TestLogger::class, $thing->logger);

        $thing->run();

        /** @var TestLogger $logger */
        $logger = $di(LoggerInterface::class);

        $this->assertTrue($logger->hasRecords('info'), 'Logger was not shared :(');
    }
}
