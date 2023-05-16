<?php


namespace AZphpTests\DI\Auryn;


use Auryn\Injector;
use AZphp\DI\Auryn\Bootstrap;
use AZphp\DI\Constants;
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
            static function (Injector $container): Injector {
                return $container
                    ->alias(LoggerInterface::class, Logger::class)
                    ->define(Logger::class, [
                        ':name' => 'mylogger',
                        // not a good way since injector is not fully configured yet
                        //':handlers' => [$container->make(TestHandler::class)]
                    ])
                    ->prepare(Logger::class, function (Logger $logger) use ($container) {
                        $logger->pushHandler(
                            $container->make(TestHandler::class)
                        );
                    })
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
        $thing2 = $di->make(Thing::class);
        $this->assertNotEquals(spl_object_hash($thing), spl_object_hash($thing2));
        $this->assertInstanceOf(Thing::class, $thing);
        $this->assertInstanceOf(Logger::class, $thing->logger);

        $thing->run();

        /** @var Logger $logger */
        $logger = $di->make(LoggerInterface::class);
        /** @var TestHandler $handler */
        $handler = $logger->getHandlers()[0];

        $this->assertEquals(spl_object_hash($logger), spl_object_hash($thing->logger));
        $this->assertTrue($handler->hasRecords('info'), 'Logger was not shared :(');
    }
}
