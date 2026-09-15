<?php

namespace App\Controller;

use App\FizzBuzz\Dto\FizzBuzzListRequestDto;
use App\FizzBuzz\Event\FizzBuzzListGeneratedEvent;
use App\FizzBuzz\FizzBuzzGeneratorInterface;
use App\FizzBuzz\FizzBuzzSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(
        #[MapQueryString(
            serializationContext: [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false],
        )]
        FizzBuzzListRequestDto $parameters,
        Request $request,
    ): Response {
        $list = $this->fizzBuzzGenerator->generate(
            $parameters->getInt1(),
            $parameters->getInt2(),
            $parameters->getLimit(),
            $parameters->getStr1(),
            $parameters->getStr2(),
        );

        // In case that no `Accept` header is present, it falls back to *text/plain*.
        $requestedFormat = $request->getAcceptableContentTypes()[0] ?? 'text/plain';

        $this->eventDispatcher->dispatch(new FizzBuzzListGeneratedEvent($parameters, $request->getPathInfo()), FizzBuzzListGeneratedEvent::NAME);

        return new Response(
            $this->serializer->serialize($list, $this->getOutputFormat($requestedFormat)),
            Response::HTTP_OK,
            ['Content-Type' => $requestedFormat]
        );
    }

    /**
     * Gives a serialization format according to the MIME-type value of the `Accept` header.
     *
     * If no `Accept` MIME type is supported, it falls back to *csv*.
     *
     * @param string $acceptHeader MIME type of the `Accept` header
     *
     * @return string returns a supported serialization type for transforming the array given by {@see FizzBuzzGeneratorInterface}
     */
    private function getOutputFormat(string $acceptHeader): string
    {
        return match ($acceptHeader) {
            'application/json' => 'json',
            default => 'csv',
        };
    }
}
