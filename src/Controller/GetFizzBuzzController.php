<?php

namespace App\Controller;

use App\FizzBuzz\Dto\FizzBuzzListRequest;
use App\FizzBuzz\FizzBuzzGeneratorInterface;
use App\FizzBuzz\FizzBuzzSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Exposes the endpoint that returns a fizzbuzz list given the specific parameters.
 */
#[Route('/fizzbuzz', name: 'fizzbuzz')]
final class GetFizzBuzzController extends AbstractController
{
    public function __construct(
        private readonly FizzBuzzGeneratorInterface $fizzBuzzGenerator,
        #[Autowire(service: FizzBuzzSerializer::class)]
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] FizzBuzzListRequest $parameters,
        Request $request,
    ): Response {
        $list = $this->fizzBuzzGenerator->generate(
            $parameters->getInt1(),
            $parameters->getInt2(),
            $parameters->getLimit(),
            $parameters->getStr1(),
            $parameters->getStr2(),
        );
        $requestedFormat = $request->getAcceptableContentTypes()[0];

        return new Response(
            $this->serializer->serialize($list, $this->getOutputFormat($requestedFormat)),
            Response::HTTP_OK,
            ['Content-Type' => $requestedFormat]
        );
    }

    private function getOutputFormat(string $acceptHeader): string
    {
        return match (true) {
            'application/json' === $acceptHeader => 'json',
            default => 'csv',
        };
    }
}
