<?php
namespace Sportscore\Shared\Exceptions;

class DomainException extends \Exception
{
    public static function notFound(string $entity, int|string $id): self
    {
        return new self("$entity with ID $id not found.");
    }

    public static function invalidStatus(string $status): self
    {
        return new self("Invalid match status: $status");
    }

    public static function invalidScore(int $score): self
    {
        return new self("Score cannot be negative: $score");
    }
}
