<?php

namespace VicidialApi\ValueObjects;

final class BasicAuth
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
    ) {
    }

    public static function from(string $username, string $password): self
    {
        return new self($username, $password);
    }

    public function encode(): string
    {
        return base64_encode("{$this->username}:{$this->password}");
    }
}
