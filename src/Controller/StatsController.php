<?php

namespace App\Controller;

use App\Stats\UrlStats;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Controller that exposes the stats of the API's FizzBuzz endpoint.
 */
#[Route('/stats', name: 'stats', methods: ['GET'])]
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
    public function __invoke(Request $request): Response
    {
        if ($request->query->count() > 0) {
            throw new BadRequestHttpException('This endpoint does not accept parameters');
        }
        $stats = $this->stats->getMostPopularUrl();
        if (null === $stats) {
            return new Response('{}', Response::HTTP_OK, ['Content-Type' => 'application/json']);
        }

        return new Response($this->serializer->serialize($stats, 'json'), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
