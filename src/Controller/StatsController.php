<?php

namespace App\Controller;

use App\Stats\UrlStats;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/stats', name: 'stats')]
final class StatsController extends AbstractController
{
    public function __construct(
        private readonly UrlStats $stats,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(): Response
    {
        $stats = $this->stats->getMostPopularUrl();
        if (null === $stats) {
            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new Response($this->serializer->serialize($stats, 'json'), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
