<?php

namespace App\Http\Controllers\Web\Auth;

use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Services\Auth\UserSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        protected UserSessionService $sessionService
    ) {}

    public function showLoginForm(): View
    {
        $testCredentials = null;
        if (app()->environment('local') && config('testing.users')) {
            $testCredentials = [
                'password' => config('testing.password'),
                'users' => config('testing.users'),
            ];
        }

        return view('auth.login', compact('testCredentials'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'El correo es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => [__('Las credenciales no son correctas.')],
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->verification_status === VerificationStatus::RegisteredPendingEmail) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('message', 'Debes verificar tu correo antes de iniciar sesión. Revisa tu bandeja de entrada.');
        }

        if ($user->verification_status === VerificationStatus::EmailVerifiedPendingOtp) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('otp.form')->with('email', $user->email)->with('message', 'Introduce el código de 6 dígitos que te enviamos al correo.');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->sessionService->destroyCurrent($request);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
