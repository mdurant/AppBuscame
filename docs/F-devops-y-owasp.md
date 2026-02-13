# F. DevOps y checklist OWASP

## Docker (entorno local)

- **docker-compose.yml:** servicios `app` (PHP 8.2-FPM), `nginx` (puerto 8080), `mysql` (8.0, puerto 3306), `redis`, `mailhog` (SMTP 1025, UI 8025).
- **Dockerfile:** imagen PHP-FPM con extensiones pdo_mysql, zip, gd, bcmath, intl; usuario no root.
- **Uso:**  
  - `docker compose up -d`  
  - Variables en `.env`: `DB_HOST=mysql`, `REDIS_HOST=redis`, `MAIL_HOST=mailhog`, `MAIL_PORT=1025`.  
  - Dentro del contenedor: `php artisan migrate`, `php artisan db:seed`, `php artisan queue:work`.

## Pipeline CI/CD (GitHub Actions)

- **Workflow:** `.github/workflows/ci.yml`
- **Jobs:**
  1. **lint:** Pint (PSR-12) en modo `--test`.
  2. **test:** MySQL 8 servicio; `php artisan migrate --force` y `php artisan test` con `.env` de testing.
  3. **static-analysis:** PHPStan opcional (`continue-on-error: true`).
- **Triggers:** push y pull_request en `main` y `develop`.

Para **despliegue** (build/deploy), añadir un job que dependa de `test` y ejecute el deploy (por ejemplo, deploy a servidor o a un PaaS) según el entorno.

## Checklist OWASP Top 10 aplicado al proyecto

| Riesgo OWASP | Medida en el proyecto |
|--------------|------------------------|
| **A01:2021 – Broken Access Control** | Policies (PropertyPolicy); `authorize()` en controllers; rutas dashboard con `auth:sanctum`; ownership por `user_id`. |
| **A02:2021 – Cryptographic Failures** | Contraseñas con `password => 'hashed'` (bcrypt); tokens de verificación y OTP guardados hasheados; 2FA secret cifrado con `Crypt::encryptString`. |
| **A03:2021 – Injection** | Eloquent y consultas parametrizadas; validación con FormRequest/validate(); sin concatenación SQL. |
| **A04:2021 – Insecure Design** | Flujo registro → email → OTP; no revelar si un email existe (mensajes genéricos en resend/verify); rate limiting en auth. |
| **A05:2021 – Security Misconfiguration** | `.env` fuera de repo; `APP_DEBUG=false` en producción; CORS configurable; headers seguros (HTTPS en prod). |
| **A06:2021 – Vulnerable Components** | `composer audit` en CI (opcional); dependencias actualizadas. |
| **A07:2021 – Auth Failures** | Contraseña con reglas (Password::min(8)->letters()->numbers()...); OTP 15 min; 2FA opcional; sesiones/tokens con Sanctum. |
| **A08:2021 – Software and Data Integrity** | Composer lock; en producción verificar checksums o firmas si se usan artefactos externos. |
| **A09:2021 – Logging/Monitoring** | Logs en Laravel (storage/logs); health check `/up`; considerar logs estructurados y trazas en producción. |
| **A10:2021 – SSRF** | GeocodingService solo llama a URL conocida (Google); validar y limitar URLs si se exponen parámetros. |

**Adicional:** CSRF en rutas web (middleware por defecto); CORS en API; rate limiting (throttle) en registro, login, reenvío email/OTP; no eliminar mensajes (auditoría).

---

## Decisiones tomadas

- Docker con PHP-FPM + Nginx + MySQL + Redis + MailHog para desarrollo y pruebas locales.
- CI con lint (Pint), tests (PHPUnit) y análisis estático opcional (PHPStan).
- OWASP cubierto con políticas de acceso, hashing/cifrado, validación, rate limits y mensajes que no facilitan enumeración.

---

## Pendientes / Suposiciones

- CD (deploy automático) no definido; se deja para configurar según entorno (Forge, Envoyer, GitLab CI, etc.).
- Health check `/up` existe por defecto en Laravel 12; para observabilidad avanzada añadir endpoint de readiness (DB, Redis).
- `composer audit` puede añadirse como paso en el job de lint o test.
