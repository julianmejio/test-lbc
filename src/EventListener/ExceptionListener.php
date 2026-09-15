<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: KernelEvents::EXCEPTION, method: 'onKernelException')]
readonly class ExceptionListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof HttpException) {
            $this->handleNonHttpException($event);

            return;
        }
        $this->handleHttpException($event);
    }

    private function handleNonHttpException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        match (true) {
            $exception instanceof \OverflowException => $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST)),
            $exception instanceof \ValueError => $event->setResponse(new JsonResponse(['error' => 'Input numbers cannot be processed because it could go beyond maximum computational allowance'], Response::HTTP_BAD_REQUEST)),
            $exception instanceof ResourceNotFoundException => $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND)),
            default => $this->onUnexpectedError($event, $exception, $exception::class),
        };
    }

    private function handleHttpException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof HttpException) {
            return;
        }

        $previous = $exception->getPrevious();

        if ($previous instanceof ValidationFailedException) {
            $event->setResponse($this->onValidationError($previous));

            return;
        }

        $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], $exception->getStatusCode()));
    }

    private function onValidationError(ValidationFailedException $e): JsonResponse
    {
        $violations = [];
        foreach ($e->getViolations() as $violation) {
            $violations[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return new JsonResponse(['error' => 'Parameters for Fizzbuzz list are invalid. Fix the violations and try again.', 'violations' => $violations], Response::HTTP_BAD_REQUEST);
    }

    private function onUnexpectedError(ExceptionEvent $event, \Throwable $exception, string $exceptionClass): void
    {
        $this->logger->error('Unexpected error happened', [
            'exceptionClass' => $exceptionClass,
            'message' => $exception->getMessage(),
        ]);
        $event->setResponse(new JsonResponse(['error' => 'An unexpected error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}
