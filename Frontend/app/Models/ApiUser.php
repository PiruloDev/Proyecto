<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Lightweight Authenticatable implementation for API-based authentication.
 * No database table required – used only to satisfy Fortify's contract
 * after a successful registration through the Spring Boot API.
 */
class ApiUser implements Authenticatable
{
    protected array $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getAuthIdentifierName(): string
    {
        return 'email';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->attributes['email'] ?? null;
    }

    public function getAuthPassword(): string
    {
        return '';
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
        // No-op: API-based auth does not use remember tokens
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }
}
