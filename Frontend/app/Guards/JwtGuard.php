<?php

namespace App\Guards;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use App\Services\Auth\AuthService;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class JwtGuard implements Guard
{
    protected $provider;
    protected $request;
    protected $user;
    protected $authService;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
        $this->authService = new AuthService();
    }

    /**
     * Determine if the current user is authenticated.
     */
    public function check(): bool
    {
        return !is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     */
    public function guest(): bool
    {
        return !$this->check();
    }

    /**
     * Get the currently authenticated user.
     */
    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = $this->request->session()->get('jwt_token');

        if (!$token) {
            return null;
        }

        // Validar token con el backend
        if (!$this->authService->isTokenValid($token)) {
            $this->request->session()->forget('jwt_token');
            return null;
        }

        // Decodificar token y crear usuario virtual
        $payload = $this->authService->decodeToken($token);
        
        if ($payload) {
            $this->user = new User([
                'id' => $payload['sub'] ?? $payload['id'] ?? null,
                'nombre' => $payload['nombre'] ?? $payload['name'] ?? '',
                'email' => $payload['email'] ?? '',
                'rol' => $payload['rol'] ?? $payload['role'] ?? '',
            ]);
        }

        return $this->user;
    }

    /**
     * Get the ID for the currently authenticated user.
     */
    public function id()
    {
        if ($user = $this->user()) {
            return $user->id;
        }

        return null;
    }

    /**
     * Validate a user's credentials.
     */
    public function validate(array $credentials = []): bool
    {
        $result = $this->authService->login(
            $credentials['email'] ?? '',
            $credentials['password'] ?? ''
        );

        return $result['success'] ?? false;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     */
    public function attemptLogin(string $email, string $password): array
    {
        $result = $this->authService->login($email, $password);

        if ($result['success']) {
            // Guardar token en sesión
            $this->request->session()->put('jwt_token', $result['token']);
            
            // Crear usuario virtual
            $userData = $result['user'] ?? [];
            $this->user = new User([
                'id' => $userData['id'] ?? null,
                'nombre' => $userData['nombre'] ?? '',
                'email' => $userData['email'] ?? $email,
                'rol' => $result['role'] ?? $userData['rol'] ?? '',
            ]);

            return [
                'success' => true,
                'user' => $this->user,
            ];
        }

        return [
            'success' => false,
            'message' => $result['message'] ?? 'Credenciales incorrectas',
        ];
    }

    /**
     * Set the current user.
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Log the user out of the application.
     */
    public function logout(): bool
    {
        $token = $this->request->session()->get('jwt_token');
        
        if ($token) {
            $this->authService->logout($token);
        }

        $this->request->session()->forget('jwt_token');
        $this->user = null;

        return true;
    }
}
