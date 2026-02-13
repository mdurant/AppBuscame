# C. Data Model

## Modelo entidad-relación (descrito)

### Auth y usuarios

- **users**  
  - id, name, email, email_verified_at, password, verification_status (enum: registered_pending_email, email_verified_pending_otp, fully_verified), remember_token, created_at, updated_at.  
  - Índices: email (unique).

- **profiles**  
  - id, user_id (FK users), first_name, last_name, avatar_path, phone, created_at, updated_at.  
  - Índice: user_id (unique).

- **sessions** (sesiones abiertas / tokens para “sesiones activas”)  
  - id, user_id (FK users), token_hash, ip_address, user_agent, last_activity_at, created_at, updated_at.  
  - Índices: user_id, token_hash (unique), last_activity_at (para limpieza).

- **email_verifications**  
  - id, user_id (FK users), email, token_hash, expires_at, verified_at, created_at, updated_at.  
  - Índices: user_id, token_hash, expires_at.

- **otp_codes**  
  - id, user_id (FK users), code_hash (no guardar código en claro), purpose (e.g. post_email_verify), expires_at, used_at, created_at, updated_at.  
  - Índices: user_id, (user_id, purpose) para “último OTP por propósito”, expires_at.

- **two_factor_secrets**  
  - id, user_id (FK users), secret_encrypted (cifrado at-rest), confirmed_at, created_at, updated_at.  
  - Índice: user_id (unique).

- **backup_codes**  
  - id, user_id (FK users), code_hash, used_at, created_at, updated_at.  
  - Índices: user_id, code_hash.

---

### Propiedades y direcciones

- **properties**  
  - id, user_id (FK users, ofertante), type (enum: casa, departamento, etc.), status (enum: draft, pending_payment, published, paused, archived), rental_type (días/meses), cost_amount, cost_currency, check_in_time, check_out_time, includes_cleaning (boolean), completeness_percent (0-100), published_at, created_at, updated_at.  
  - Índices: user_id, status, (status, published_at) para listados.

- **property_addresses**  
  - id, property_id (FK properties, unique), address_line, city, region, postal_code, country, latitude, longitude, place_id (Google), created_at, updated_at.  
  - Índices: property_id (unique), (latitude, longitude) para búsqueda geo.

- **property_photos**  
  - id, property_id (FK properties), path, sort_order, created_at, updated_at.  
  - Índice: (property_id, sort_order). Constraint: máximo 5 por property_id (lógica en app o trigger).

- **availability_ranges**  
  - id, property_id (FK properties), start_date, end_date, created_at, updated_at.  
  - Índices: property_id, (start_date, end_date).

---

### Suscripciones y pagos

- **subscriptions**  
  - id, user_id (FK users), plan_slug, status (enum: active, cancelled, expired), starts_at, ends_at, created_at, updated_at.  
  - Índices: user_id, status, ends_at.

- **payments**  
  - id, user_id (FK users), payable_type, payable_id (polimórfico: subscription, one-time), amount, currency, status (pending, completed, failed, refunded), gateway, gateway_reference, metadata (JSON), paid_at, created_at, updated_at.  
  - Índices: user_id, status, gateway_reference, (payable_type, payable_id).

- **invoices** (recibos/facturas)  
  - id, user_id (FK users), payment_id (FK payments, nullable), number (unique), type (subscription, rental, commission), amount, currency, status, file_path (PDF), issued_at, created_at, updated_at.  
  - Índices: user_id, payment_id, number (unique).

---

### Arriendos (opcional para historial)

- **rentals**  
  - id, property_id (FK properties), renter_id (FK users), subscription_id (FK subscriptions, nullable), status, start_date, end_date, total_amount, created_at, updated_at.  
  - Índices: property_id, renter_id, status.

- **rental_events**  
  - id, rental_id (FK rentals), type (created, confirmed, cancelled, completed), payload (JSON), created_at.  
  - Índice: rental_id, created_at.

---

### Mensajería (sin borrado; auditoría)

- **message_threads**  
  - id, property_id (FK properties), created_at, updated_at.  
  - Índice: property_id.  
  - Participantes: tabla intermedia thread_user (thread_id, user_id, role: owner/renter) o derivado por property owner + quien inicia.

- **thread_participants**  
  - id, message_thread_id (FK message_threads), user_id (FK users), role (owner, renter), created_at, updated_at.  
  - Índice único: (message_thread_id, user_id).

- **messages**  
  - id, message_thread_id (FK message_threads), user_id (FK users), body (text), created_at, updated_at.  
  - No soft delete; no deleted_at. Índices: message_thread_id, created_at, user_id.

- **message_events** (auditoría: estados, entregado, leído)  
  - id, message_id (FK messages), event_type (sent, delivered, read), user_id (quién dispara si aplica), payload (JSON), created_at.  
  - Índices: message_id, created_at.

---

### Scoring y verificación

- **property_scores**  
  - id, property_id (FK properties), overall_score (decimal), verification_status (enum: unverified, pending, verified), last_calculated_at, created_at, updated_at.  
  - Índice: property_id (unique).

- **score_factors** (config o snapshot de factores)  
  - id, property_score_id (FK property_scores), factor_key (completeness, address_validated, photos_count, history, reputation), weight, value, created_at, updated_at.  
  - Índice: property_score_id.

- **score_history** (auditoría de cambios de score)  
  - id, property_id (FK properties), previous_score, new_score, factors_snapshot (JSON), calculated_at, created_at.  
  - Índices: property_id, calculated_at.

---

### Soporte

- **tickets**  
  - id, user_id (FK users), subject, status (open, in_progress, resolved, closed), created_at, updated_at.  
  - Índices: user_id, status.

- **ticket_replies**  
  - id, ticket_id (FK tickets), user_id (FK users, nullable para sistema), body, is_staff (boolean), created_at, updated_at.  
  - Índice: ticket_id, created_at.

- **faq_entries**  
  - id, question, answer, sort_order, category, is_published, created_at, updated_at.  
  - Índice: category, is_published.

---

## Constraints e índices resumidos

| Tabla | Unicos | Compuestos / performance |
|-------|--------|---------------------------|
| users | email | — |
| profiles | user_id | — |
| sessions | token_hash | user_id, last_activity_at |
| email_verifications | — | user_id, expires_at |
| otp_codes | — | (user_id, purpose), expires_at |
| two_factor_secrets | user_id | — |
| properties | — | user_id, (status, published_at) |
| property_addresses | property_id | (latitude, longitude) |
| property_photos | — | (property_id, sort_order) |
| subscriptions | — | user_id, status, ends_at |
| payments | — | user_id, gateway_reference, (payable_type, payable_id) |
| invoices | number | user_id, payment_id |
| message_threads | — | property_id |
| thread_participants | (message_thread_id, user_id) | — |
| messages | — | message_thread_id, created_at |
| message_events | — | message_id, created_at |
| property_scores | property_id | — |
| score_history | — | property_id, calculated_at |
| tickets | — | user_id, status |

- **Integridad:** FK con `onDelete` según regla de negocio (ej. property_id en photos → cascade; user_id en sessions → cascade).
- **Límite 5 fotos:** restricción en aplicación o check constraint / trigger en BD.

---

## Decisiones tomadas

- Tokens y códigos sensibles solo almacenados hasheados (email verification token, OTP, backup codes); 2FA secret cifrado at-rest.
- Mensajes y eventos de mensajería sin `deleted_at`; trazabilidad vía `message_events`.
- Scoring con tabla de factores y tabla de historial para auditoría y recálculo.
- Pagos polimórficos (payable_type / payable_id) para suscripciones y otros cobros futuros.

---

## Pendientes / Suposiciones

- `verification_status` en users puede vivir en tabla `users` o en `profiles`; se deja en users para flujo de auth.
- Multi-tenant: no se añade `tenant_id`; si más adelante se necesita, se agrega en users, properties, subscriptions, payments.
- Rentals/rental_events son opcionales para “historial de arriendos”; se incluyen en el modelo para completitud.
