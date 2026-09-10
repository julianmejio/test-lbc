<?php

namespace App\Controller;

use App\FizzBuzz\Dto\FizzBuzzListRequest;
use App\FizzBuzz\FizzBuzzGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fizzbuzz')]
final class FizzBuzzController extends AbstractController
{
    public function __construct(private readonly FizzBuzzGeneratorInterface $fizzBUzzGenerator)
    {
    }

    #[Route('/', name: 'app_fizz_buzz')]
    public function index(#[MapQueryString] FizzBuzzListRequest $parameters): JsonResponse
    {
        $list = $this->fizzBUzzGenerator->generate(
            $parameters->getInt1(),
            $parameters->getInt2(),
            $parameters->getLimit(),
            $parameters->getStr1(),
            $parameters->getStr2(),
        );
        return $this->json([
            'list' => $list,
        ]);
    }
}
