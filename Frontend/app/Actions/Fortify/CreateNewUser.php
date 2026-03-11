<?php

namespace App\Actions\Fortify;

use App\Services\AuthService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Validate and create a newly registered user via the Spring Boot API.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): Authenticatable
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => $this->passwordRules(),
        ])->validate();

        $result = $this->authService->registrarCliente([
            'nombre' => $input['name'],
            'email' => $input['email'],
            'telefono' => $input['telefono'] ?? null,
            'password' => $input['password'],
        ]);

        if (!$result['success']) {
            throw ValidationException::withMessages([
                'email' => [$result['mensaje'] ?? 'Error al registrar usuario'],
            ]);
        }

        // Return a minimal Authenticatable object for Fortify's flow
        return new \App\Models\ApiUser([
            'name' => $input['name'],
            'email' => $input['email'],
        ]);
    }
}
