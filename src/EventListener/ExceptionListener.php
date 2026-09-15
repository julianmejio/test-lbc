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
class ExceptionListener
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * Handles exception and converts them into JSON-compatible messages with proper HTTP status codes.
     *
     * @param ExceptionEvent $event exception thrown by the kernel
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof HttpException) {
            $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST));

            return;
        }
        $previous = $exception->getPrevious();
        if (null === $previous) {
            $event->setResponse(new JsonResponse(['error' => 'An error has been occurred'], Response::HTTP_INTERNAL_SERVER_ERROR));

            return;
        }

        match ($previous::class) {
            ValidationFailedException::class => $event->setResponse($this->onValidationError($previous)),
            ResourceNotFoundException::class => $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND)),
            default => $this->onUnexpectedError($event, $previous, $previous::class),
        };
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
        $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}
