<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|max:255',
            'confirm_password' => 'required|same:password',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'confirm_password.required' => 'Debes confirmar tu contraseña',
            'confirm_password.same' => 'Las contraseñas no coinciden',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
            'telefono' => $request->telefono ?? '',
            'contrasena' => $request->password,
        ];

        $response = $this->apiService->post('/auth/registro/cliente', $data);

        if ($response['success']) {
            return redirect()->route('login')
                ->with('success', '¡Registro exitoso! Ya puedes iniciar sesión con tus credenciales.');
        }

        return redirect()->back()
            ->with('error', $response['message'] ?? 'Error al registrar el cliente')
            ->withInput();
    }
}
