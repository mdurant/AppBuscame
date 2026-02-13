# E. Implementación (código ejemplo)

## Resumen de lo implementado

- **Controllers:** API Auth (Register, EmailVerification, OTP), API Property (público y dashboard), con policies y autorización.
- **Services:** OtpService, EmailVerificationService, TwoFactorService (TOTP con fallback), ScoringService, GeocodingService.
- **Events/Listeners:** UserRegistered → SendVerificationEmail; OtpRequested → SendOtpEmail; TwoFactorEnabled → SendBackupCodesEmail; PropertyPublished → RecalculatePropertyScore; MessageSent → RecordMessageEvent.
- **Jobs:** SendEmailJob, ProcessPhotosJob, CalculatePropertyScoreJob.
- **Migrations:** users (verification_status), profiles, user_sessions, email_verifications, otp_codes, two_factor_secrets, backup_codes, properties, property_addresses, property_photos, availability_ranges, subscriptions, payments, invoices, message_threads, thread_participants, messages, message_events, property_scores, score_factors, score_history, tickets, ticket_replies, faq_entries, personal_access_tokens (Sanctum).
- **Models:** User, Profile, Auth/*, Property/*, Billing/*, Messaging/*, Scoring/*, Support/*.
- **Seeders/Factories:** UserSeeder (español), PropertySeeder, FaqSeeder; UserFactory, PropertyFactory.
- **Tests:** AuthRegistrationTest, PropertyPublishTest, MessagingTest, ScoringTest.

## Dependencias añadidas

- `laravel/sanctum` (API tokens).
- Para 2FA TOTP en producción: `composer require pragmarx/google2fa` y descomentar/activar la verificación real en `TwoFactorService`.

## Cómo ejecutar

```bash
composer install
cp .env.example .env && php artisan key:generate
# Configurar .env: DB_*, MAIL_*, QUEUE_CONNECTION=redis (o database), GOOGLE_MAPS_API_KEY opcional
php artisan migrate
php artisan db:seed
php artisan test
```

## Decisiones tomadas

- Auth con Sanctum; policies registradas en AppServiceProvider para el modelo anidado Property.
- 2FA sin dependencia externa en desarrollo (acepta cualquier código 6 dígitos); en producción usar pragmarx/google2fa.
- Scoring con factores configurables y tabla de historial; job encolado tras PropertyPublished.
- Mensajería: eventos de auditoría (message_events) creados por listener; mensajes sin soft delete.

## Pendientes / Suposiciones

- Login (POST /api/login) no implementado en este bloque; usar Sanctum con email/password y comprobar verification_status.
- Endpoints de sesiones, cambio de contraseña, 2FA setup/confirm y mensajes (threads, enviar mensaje) quedan esbozados en D; implementación completa en siguientes iteraciones.
- Pagos: sin integración real con gateway; flujo 402 y redirect_to_payment listo para conectar Stripe/Mercado Pago.
