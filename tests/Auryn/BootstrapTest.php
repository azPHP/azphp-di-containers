<?php


namespace AZphpTests\DI\Auryn;


use Auryn\Injector;
use AZphp\DI\Auryn\Bootstrap;
use AZphp\DI\Constants;
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
            static function (Injector $container): Injector {
                return $container->alias(LoggerInterface::class, TestLogger::class)
                    ->share(LoggerInterface::class);
            },
            // specify api key for Thing
            static function (Injector $container): Injector {
                return $container->define(Thing::class, [':apiKey' => Constants::THING_API_KEY]);
            },
        ];

        $bootstrap->prepare($configs);

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
