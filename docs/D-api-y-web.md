# D. API + Web

## Endpoints REST (y Web donde aplique)

### Auth

| Método | Ruta | Auth | Descripción | Request ejemplo | Response / Códigos |
|--------|------|------|-------------|------------------|---------------------|
| POST | `/api/register` | No | Registro | `{ "first_name", "last_name", "email", "password", "password_confirmation" }` | 201 + user (sin password), 422 validación, 429 rate limit |
| POST | `/api/login` | No | Login | `{ "email", "password" }` | 200 + token + user, 401 credenciales, 403 no verificado, 429 |
| POST | `/api/email/verify` | No (token en query) | Verificar email (link) | GET con `?token=...` desde email | 200 OK, 400/410 token inválido/expirado |
| POST | `/api/email/resend` | No | Reenviar verificación | `{ "email" }` | 202, 422 email no encontrado, 429 (60s) |
| POST | `/api/otp/verify` | No (session/temp token) | Verificar OTP 6 dígitos | `{ "email", "code" }` o con token temporal | 200 + token, 422 código inválido/expirado |
| POST | `/api/otp/resend` | No | Renovar OTP | `{ "email" }` | 202, 422, 429 (60s) |
| POST | `/api/logout` | Bearer | Cerrar sesión actual | — | 204 |
| GET | `/api/user` | Bearer | Usuario actual | — | 200 + user + profile, 401 |
| PUT | `/api/user/profile` | Bearer | Actualizar perfil + avatar | multipart/form: first_name, last_name, avatar | 200, 422, 401 |
| GET | `/api/sessions` | Bearer | Listar sesiones abiertas | — | 200 + sessions, 401 |
| DELETE | `/api/sessions/{id}` | Bearer | Cerrar sesión | — | 204, 403/404 |
| PUT | `/api/password` | Bearer | Cambio contraseña | `{ "current_password", "password", "password_confirmation" }` | 200, 422, 401 |
| POST | `/api/2fa/setup` | Bearer | Iniciar 2FA (QR + secret) | — | 200 + qr_url, secret (masked), 409 ya activo |
| POST | `/api/2fa/confirm` | Bearer | Confirmar 2FA con código TOTP | `{ "code" }` | 200 + backup_codes (una vez), 422 código inválido |
| POST | `/api/2fa/disable` | Bearer | Desactivar 2FA | `{ "password", "code" }` (TOTP o backup) | 204, 422, 401 |
| POST | `/api/2fa/backup-codes` | Bearer | Regenerar backup codes | `{ "password" }` | 200 + backup_codes, 422, 401 |

**Web (mismo flujo, rutas web):**  
`/register`, `/login`, `/email/verify`, `/otp/verify`, `/dashboard`, `/dashboard/profile`, `/dashboard/security`, `/dashboard/sessions`, etc.  
Respuestas HTML + redirecciones; CSRF en formularios.

---

### Properties

| Método | Ruta | Auth | Descripción | Request ejemplo | Response / Códigos |
|--------|------|------|-------------|------------------|---------------------|
| GET | `/api/properties` | No (público) o Bearer | Listado (filtros) | query: status=published, type, lat, lng, radius | 200 paginado, 429 |
| GET | `/api/properties/{id}` | No | Detalle público | — | 200, 404 |
| GET | `/api/dashboard/properties` | Bearer | Mis propiedades | — | 200 paginado, 401 |
| POST | `/api/dashboard/properties` | Bearer | Crear borrador | body: type, rental_type, cost_amount, check_in_time, check_out_time, includes_cleaning, address_line, city, ... | 201, 422, 401 |
| PUT | `/api/dashboard/properties/{id}` | Bearer (owner) | Actualizar (draft) | igual que crear, parcial | 200, 422, 403, 404 |
| POST | `/api/dashboard/properties/{id}/photos` | Bearer (owner) | Subir foto (máx 5) | multipart: photo | 201, 422 (límite), 403 |
| DELETE | `/api/dashboard/properties/{id}/photos/{photoId}` | Bearer (owner) | Quitar foto | — | 204, 403, 404 |
| POST | `/api/dashboard/properties/{id}/geocode` | Bearer | Geocodificar dirección | `{ "address_line", "city", "region", "country" }` | 200 + lat, lng, place_id, 422 |
| POST | `/api/dashboard/properties/{id}/publish` | Bearer (owner) | Pasar a publicar (→ pending_payment o published) | — | 200 + redirect_to_payment si aplica, 422 (completitud), 403 |
| POST | `/api/dashboard/properties/{id}/pause` | Bearer (owner) | Pausar | — | 200, 403, 404 |
| POST | `/api/dashboard/properties/{id}/archive` | Bearer (owner) | Archivar | — | 200, 403, 404 |

**Policies:** Solo el `owner` (user_id) puede editar/pausar/archivar/subir fotos. Public list no requiere auth.

---

### Billing (suscripción, pagos, historial)

| Método | Ruta | Auth | Descripción | Request ejemplo | Response / Códigos |
|--------|------|------|-------------|------------------|---------------------|
| GET | `/api/dashboard/subscription` | Bearer | Estado suscripción | — | 200 + subscription, 401 |
| POST | `/api/dashboard/subscription` | Bearer | Crear/activar suscripción | `{ "plan_slug", "payment_method_id" }` (según gateway) | 201/200, 422, 402 payment failed |
| GET | `/api/dashboard/payments` | Bearer | Historial pagos | query: page, per_page | 200 paginado, 401 |
| GET | `/api/dashboard/payments/{id}` | Bearer (owner) | Detalle pago + recibos | — | 200, 403, 404 |
| GET | `/api/dashboard/invoices` | Bearer | Listado facturas/recibos | query: page | 200 paginado, 401 |
| GET | `/api/dashboard/invoices/{id}/download` | Bearer (owner) | Descargar PDF | — | 200 file, 403, 404 |

**Policies:** Solo el usuario dueño del recurso (user_id en subscription/payment/invoice).

---

### Messaging

| Método | Ruta | Auth | Descripción | Request ejemplo | Response / Códigos |
|--------|------|------|-------------|------------------|---------------------|
| GET | `/api/dashboard/threads` | Bearer | Mis conversaciones | query: page | 200 paginado, 401 |
| GET | `/api/dashboard/threads/{id}` | Bearer (participant) | Ver thread + mensajes | query: since (cursor) | 200 + messages, 403, 404 |
| POST | `/api/dashboard/threads` | Bearer | Crear thread (contactar por propiedad) | `{ "property_id", "initial_message" }` | 201 + thread, 422, 403 (no duplicar si existe) |
| POST | `/api/dashboard/threads/{id}/messages` | Bearer (participant) | Enviar mensaje | `{ "body" }` | 201 + message, 422, 403 |
| PATCH | `/api/dashboard/threads/{id}/messages/{msgId}/read` | Bearer (participant) | Marcar leído | — | 200, 403, 404 |
| GET | `/api/dashboard/threads/{id}/events` | Bearer (participant) | Auditoría/trazabilidad | query: message_id (opcional) | 200 + events, 403 |

**Policies:** Solo participantes del thread pueden leer/escribir. Soporte (admin) puede tener policy separada para ver todos.

---

### Support (FAQ, tickets, contacto)

| Método | Ruta | Auth | Descripción | Request ejemplo | Response / Códigos |
|--------|------|------|-------------|------------------|---------------------|
| GET | `/api/faq` | No | Listado FAQ por categoría | query: category | 200, 429 |
| GET | `/api/kb` | No | Base conocimiento (artículos) | query: q, category | 200, 429 |
| POST | `/api/contact` | No | Formulario contacto | `{ "name", "email", "subject", "message" }` | 202, 422, 429 |
| POST | `/api/dashboard/tickets` | Bearer | Crear ticket | `{ "subject", "message" }` | 201, 422, 401 |
| GET | `/api/dashboard/tickets` | Bearer | Mis tickets | query: page, status | 200 paginado, 401 |
| GET | `/api/dashboard/tickets/{id}` | Bearer (owner) | Detalle ticket + respuestas | — | 200, 403, 404 |
| POST | `/api/dashboard/tickets/{id}/replies` | Bearer (owner) | Responder ticket | `{ "body" }` | 201, 422, 403 |

**Policies:** Usuario solo ve sus tickets. Admin/soporte: policy `viewAny` para listar todos.

---

### Scoring (admin o interno)

| Método | Ruta | Auth | Descripción | Request ejemplo | Response / Códigos |
|--------|------|------|-------------|------------------|---------------------|
| GET | `/api/properties/{id}/score` | No (público) o Bearer | Score y estado verificación | — | 200 + score, verification_status, 404 |
| POST | `/api/admin/properties/{id}/recalculate-score` | Bearer (admin) | Recalcular score | — | 200, 403, 404 |

---

## Policies / Permissions (resumen)

| Rol/Contexto | Recurso | Permisos |
|--------------|---------|----------|
| Guest | Properties (public) | index, show |
| User (fully_verified) | Own profile, sessions, 2FA | full (own) |
| User | Own properties | create, update, delete photos, publish, pause, archive (owner) |
| User | Subscription, payments, invoices | view own only |
| User (participant) | Threads, messages | view, create message, mark read (own threads) |
| User | Tickets | view own, create, reply |
| Admin / Support | Tickets | viewAny, view all, reply as staff |
| Admin | Properties (scoring) | recalculate-score |
| System | Jobs/Queues | — |

Implementación: Laravel Policies (`PropertyPolicy`, `ThreadPolicy`, `TicketPolicy`, etc.) y gates si se necesita `admin`.

---

## Rate limits y protecciones

- **Registro:** 5 por minuto por IP (evitar spam).
- **Login:** 5 por minuto por IP + 10 por minuto por email (evitar enumeración).
- **Reenvío email verificación / OTP:** 3 por minuto por email (60s entre envíos en UX).
- **API global (autenticada):** 60/min por user; 120/min para listados GET.
- **API pública (listados, FAQ):** 30/min por IP.
- **Contact / tickets:** 10/min por user o IP.
- **Geocoding:** 30/min por user (límite costes API externa).
- **Subida fotos:** 20/min por user.

Protecciones:  
- CORS solo orígenes permitidos en API.  
- CSRF en todas las rutas web (formularios).  
- Validación entrada (FormRequest); no exponer stack en producción.  
- Respuestas de login/registro genéricas (“Credenciales incorrectas” / “Si el correo existe, recibirás un enlace”) para no enumerar usuarios.

---

## Decisiones tomadas

- API REST con autenticación Bearer (Laravel Sanctum); Web con sesión y CSRF.
- Policies por recurso (Property, Thread, Ticket, Payment, etc.) y ownership por user_id.
- Rate limiting por ruta/grupo; límites distintos para auth, público y dashboard.
- Respuestas de error estandarizadas (JSON para API) y mensajes que no faciliten enumeración.

---

## Pendientes / Suposiciones

- Pagos: request/response de ejemplo genérico; integración real (Stripe/Mercado Pago) define campos exactos.
- Admin: se asume rol `is_admin` o similar en users o tabla `roles`; no detallado en endpoints más allá de recalcular score y ver todos los tickets.
