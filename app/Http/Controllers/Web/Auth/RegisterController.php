<?php

namespace App\Http\Controllers\Web\Auth;

use App\Enums\VerificationStatus;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\TermsVersion;
use App\Models\User;
use App\Models\UserTermAcceptance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        $termsVersion = TermsVersion::orderByDesc('effective_at')->orderByDesc('id')->first();

        return view('auth.register', [
            'termsVersion' => $termsVersion,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()->symbols()],
        ];
        if (TermsVersion::exists()) {
            $rules['terms_accepted'] = ['required', 'accepted'];
            $rules['terms_version_id'] = ['required', 'exists:terms_versions,id'];
        }
        $validated = $request->validate($rules, [
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'Los apellidos son obligatorios.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'Ese correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'terms_accepted.accepted' => 'Debes aceptar los términos y condiciones.',
        ]);

        $user = User::create([
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'email' => $validated['email'],
            'password' => $validated['password'],
            'verification_status' => VerificationStatus::RegisteredPendingEmail,
        ]);

        Profile::create([
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
        ]);

        if (! empty($validated['terms_version_id'])) {
            UserTermAcceptance::create([
                'user_id' => $user->id,
                'terms_version_id' => $validated['terms_version_id'],
                'accepted_at' => now(),
                'ip_address' => $request->ip(),
            ]);
        }

        event(new UserRegistered($user));

        return redirect()->route('verify-email.sent')->with('email', $user->email);
    }
}
