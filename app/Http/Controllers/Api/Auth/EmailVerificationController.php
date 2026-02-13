<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\EmailVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function __construct(
        protected EmailVerificationService $emailVerification
    ) {}

    public function verify(Request $request): JsonResponse
    {
        $token = $request->query('token');
        if (! $token) {
            return response()->json(['message' => 'Token no proporcionado.'], 400);
        }

        $user = $this->emailVerification->verifyByToken($token);
        if (! $user) {
            return response()->json(['message' => 'Enlace inválido o expirado.'], 410);
        }

        return response()->json([
            'message' => 'Correo verificado. Introduce el código de 6 dígitos que te enviamos.',
            'verification_status' => $user->fresh()->verification_status->value,
        ]);
    }

    public function resend(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = \App\Models\User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(['message' => 'Si el correo existe, recibirás un enlace.'], 202);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'El correo ya está verificado.'], 422);
        }

        $token = $this->emailVerification->createVerification($user);
        $url = url('/email/verify?token='.$token);
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerifyEmailMail($user, $url));

        return response()->json(['message' => 'Hemos enviado un nuevo enlace a tu correo.'], 202);
    }
}
