<?php

namespace App\FizzBuzz\EventSubscriber;

use App\FizzBuzz\Event\FizzBuzzListGeneratedEvent;
use App\Stats\UrlStatsStore;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class FizzBuzzListEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private UrlStatsStore $stats, private LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FizzBuzzListGeneratedEvent::NAME => ['handleFizzBuzzListGeneratedEvent', 0],
        ];
    }

    public function handleFizzBuzzListGeneratedEvent(FizzBuzzListGeneratedEvent $event): void
    {
        $this->logger->info('Vars', get_object_vars($event->getParameters()));
        $url = '/'.http_build_query(get_object_vars($event->getParameters()));
        $this->stats->hitUrl($url);
    }
}
