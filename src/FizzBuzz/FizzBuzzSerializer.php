<?php

namespace App\FizzBuzz;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * Serializes arrays into list-compatible structures for REST API.
 */
class FizzBuzzSerializer implements SerializerInterface
{
    /**
     * @throws \JsonException
     */
    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return match ($format) {
            'json' => json_encode($data, JSON_THROW_ON_ERROR),
            default => implode(',', (array) $data),
        };
    }

    /**
     * @throws \JsonException
     */
    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        return match ($format) {
            'json' => json_decode($data, false, 512, JSON_THROW_ON_ERROR),
            default => explode(',', $data),
        };
    }
}
