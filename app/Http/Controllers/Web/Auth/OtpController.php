<?php

namespace App\Http\Controllers\Web\Auth;

use App\Enums\VerificationStatus;
use App\Events\OtpRequested;
use App\Http\Controllers\Controller;
use App\Services\Auth\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otp
    ) {}

    public function showForm(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('email') ?? $request->old('email');
        if (! $email) {
            return redirect()->route('login')->with('message', 'Introduce tu correo para recibir el código.');
        }

        $devOtp = null;
        if (app()->environment('local')) {
            $devOtp = \Illuminate\Support\Facades\Cache::get('otp_dev:'.$email);
        }

        return view('auth.otp', ['email' => $email, 'devOtp' => $devOtp]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ], [
            'code.required' => 'El código es obligatorio.',
            'code.size' => 'El código debe tener 6 dígitos.',
        ]);

        $user = \App\Models\User::where('email', $validated['email'])->first();
        if (! $user) {
            return back()->withErrors(['code' => 'Código inválido o expirado.'])->withInput();
        }

        if (! $this->otp->verify($user, $validated['code'])) {
            return back()->withErrors(['code' => 'Código inválido o expirado. Solicita uno nuevo si es necesario.'])->withInput();
        }

        $user->update(['verification_status' => VerificationStatus::FullyVerified]);
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('email');

        return redirect()->route('dashboard')->with('message', 'Sesión iniciada correctamente.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = \App\Models\User::where('email', $request->email)->first();
        if (! $user || $user->verification_status !== VerificationStatus::EmailVerifiedPendingOtp) {
            return back()->with('error', 'No se pudo renovar el código.');
        }

        $code = $this->otp->generateForUser($user);
        event(new OtpRequested($user, $code));

        return back()->with('message', 'Hemos enviado un nuevo código a tu correo.');
    }
}
