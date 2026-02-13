<?php

namespace App\Http\Controllers\Web\Auth;

use App\Events\OtpRequested;
use App\Http\Controllers\Controller;
use App\Services\Auth\EmailVerificationService;
use App\Services\Auth\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function __construct(
        protected EmailVerificationService $emailVerification,
        protected OtpService $otpService
    ) {}

    public function showSent(Request $request): View
    {
        $verificationUrl = null;
        if (app()->environment('local')) {
            $email = $request->session()->get('email');
            if ($email) {
                $verificationUrl = Cache::get('verification_url:'.$email);
            }
        }

        return view('auth.verify-email-sent', ['verificationUrl' => $verificationUrl]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $token = $request->query('token');
        if (! $token) {
            return redirect()->route('login')->with('error', 'Enlace inválido.');
        }

        $user = $this->emailVerification->verifyByToken($token);
        if (! $user) {
            return redirect()->route('login')->with('error', 'El enlace ha expirado o ya fue usado. Solicita uno nuevo.');
        }

        $code = $this->otpService->generateForUser($user);
        event(new OtpRequested($user, $code));

        return redirect()->route('otp.form')->with('email', $user->email)->with('message', 'Correo verificado. Introduce el código de 6 dígitos que te enviamos.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = \App\Models\User::where('email', $request->email)->first();
        if (! $user) {
            return back()->with('message', 'Si el correo existe, recibirás un enlace.');
        }

        if ($user->email_verified_at) {
            return redirect()->route('otp.form')->with('email', $user->email)->with('message', 'Tu correo ya está verificado. Introduce el código OTP.');
        }

        $token = $this->emailVerification->createVerification($user);
        $url = route('email.verify', ['token' => $token]);
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerifyEmailMail($user, $url));

        return back()->with('message', 'Hemos enviado un nuevo enlace a tu correo.');
    }
}
