<?php


namespace AZphp\DI;


use Psr\Log\LoggerInterface;

class Thing
{
    public LoggerInterface $logger;
    protected string $apiKey;
    protected EntityManager $entityManager;

    public function __construct(LoggerInterface $logger, string $apiKey, EntityManager $entityManager)
    {
        $this->logger = $logger;
        $this->apiKey = $apiKey;
        $this->entityManager = $entityManager;
    }

    public function run()
    {
        $this->logger->info('I can log a thing');
        return $this->apiKey;
    }
}
