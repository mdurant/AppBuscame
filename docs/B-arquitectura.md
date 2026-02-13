# B. Arquitectura

## Diagrama textual de módulos (bounded contexts) y dependencias

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              API / Web (HTTP)                                │
│  Routes (api.php, web.php) → Middleware (auth, throttle, CORS) → Controllers │
└─────────────────────────────────────────────────────────────────────────────┘
                                        │
                    ┌───────────────────┼───────────────────┐
                    ▼                   ▼                   ▼
┌───────────────────────┐ ┌───────────────────────┐ ┌───────────────────────┐
│   Auth (Bounded Ctx)   │ │  Properties (Bounded)  │ │  Billing (Bounded)    │
│  - Register            │ │  - Publish/CRUD       │ │  - Subscriptions      │
│  - Email verify        │ │  - Photos, address    │ │  - Payments           │
│  - OTP                 │ │  - Availability       │ │  - Invoices           │
│  - 2FA, sessions       │ │  - Scoring            │ │  - History            │
└───────────┬───────────┘ └───────────┬───────────┘ └───────────┬───────────┘
            │                          │                         │
            │    ┌─────────────────────┼─────────────────────┐   │
            │    │                     ▼                     │   │
            │    │  ┌─────────────────────────────────────┐  │   │
            │    └─►│  Messaging (Bounded Ctx)             │  │   │
            │       │  - Threads, messages                 │  │   │
            │       │  - Events/audit (no delete)          │  │   │
            │       └─────────────────────────────────────┘  │   │
            │                                                │   │
            │  ┌─────────────────────────────────────────────┘   │
            ▼  ▼                                                 ▼
┌───────────────────────┐                         ┌───────────────────────┐
│  Support (Bounded Ctx) │                         │  Shared / Kernel       │
│  - FAQ, KB             │                         │  - User, Profile       │
│  - Tickets             │                         │  - Events bus, Jobs    │
│  - Contact             │                         │  - Logs, health        │
└───────────────────────┘                         └───────────────────────┘
```

**Dependencias entre contextos:**
- **Auth** no depende de Properties/Billing/Messaging.
- **Properties** depende de Auth (usuario) y Billing (suscripción para publicar).
- **Billing** depende de Auth.
- **Messaging** depende de Auth y Properties (thread por propiedad/usuario).
- **Support** depende de Auth (tickets por usuario).

---

## Estructura de carpetas recomendada en Laravel 12

Opción **monolito modular** con capas por dominio (DDD-lite), sin paquetes internos separados:

```
app/
├── Domain/
│   ├── User/
│   │   ├── Models/           # User, Profile, Session, etc.
│   │   └── Enums/            # UserStatus, SessionStatus
│   ├── Auth/
│   │   ├── Models/           # EmailVerification, OtpCode, TwoFactorSecret, BackupCode
│   │   └── Enums/
│   ├── Property/
│   │   ├── Models/           # Property, PropertyPhoto, AvailabilityRange, PropertyAddress
│   │   └── Enums/            # PropertyType, PropertyStatus
│   ├── Billing/
│   │   ├── Models/           # Subscription, Payment, Invoice
│   │   └── Enums/
│   ├── Messaging/
│   │   ├── Models/           # MessageThread, Message, MessageEvent
│   │   └── Enums/
│   ├── Scoring/
│   │   ├── Models/           # PropertyScore, ScoreFactor, ScoreHistory
│   │   └── Enums/
│   └── Support/
│       ├── Models/           # Ticket, TicketReply, FaqEntry
│       └── Enums/
│
├── Application/
│   ├── Auth/                 # Application services (or use cases)
│   │   ├── RegisterUser.php
│   │   ├── VerifyEmail.php
│   │   ├── VerifyOtp.php
│   │   ├── EnableTwoFactor.php
│   │   └── ...
│   ├── Property/
│   │   ├── PublishProperty.php
│   │   ├── UpdatePropertyDraft.php
│   │   └── ...
│   ├── Billing/
│   ├── Messaging/
│   ├── Scoring/
│   └── Support/
│
├── Infrastructure/
│   ├── Auth/
│   │   ├── Repositories/     # EloquentUserRepository, etc.
│   │   └── Services/        # OtpGenerator, TotpService (adapters)
│   ├── Property/
│   │   ├── Repositories/
│   │   └── Services/        # GeocodingService (Google)
│   ├── Billing/             # PaymentGatewayAdapter
│   ├── Messaging/
│   ├── Scoring/
│   └── Notifications/       # Mail, SMS adapters
│
├── Http/
│   ├── Controllers/
│   │   ├── Api/              # API controllers por módulo
│   │   │   ├── Auth/
│   │   │   ├── Property/
│   │   │   ├── Billing/
│   │   │   ├── Messaging/
│   │   │   └── Support/
│   │   └── Web/
│   │       ├── Auth/
│   │       ├── Dashboard/
│   │       ├── Property/
│   │       └── ...
│   ├── Middleware/
│   ├── Requests/
│   │   ├── Api/
│   │   └── Web/
│   └── Resources/
│
├── Events/                   # Domain/application events
├── Listeners/
├── Jobs/                     # SendEmailJob, ProcessPhotosJob, CalculateScoreJob
├── Mail/
├── Notifications/
├── Policies/                 # Por dominio (PropertyPolicy, etc.)
└── Providers/
```

**Alternativa más simple (si se prefiere menos capas):** Mantener `app/Models` con subcarpetas por dominio (`app/Models/Property`, `app/Models/Auth`) y `app/Services` con servicios por dominio, sin carpeta `Application`/`Infrastructure` explícita. Se recomienda la estructura anterior para escalabilidad y claridad de responsabilidades.

---

## Decisiones clave

| Tema | Opción A | Opción B | Recomendación |
|------|----------|----------|----------------|
| **Monolito vs paquetes** | Monolito con módulos en carpetas (Domain/Application/Infrastructure) | Paquetes Laravel por bounded context (packages/Property, packages/Auth) | **A** para este proyecto: menos complejidad de rutas y configuración; paquetes cuando haya equipos o despliegues independientes. |
| **Colas** | Redis como driver de colas para emails, fotos, scoring | Database driver para entornos mínimos | **Redis** en producción y en Docker; database como fallback en dev si no hay Redis. |
| **Eventos** | Eventos de dominio (UserRegistered, EmailVerified, PropertyPublished) + listeners que encolan Jobs | Solo Jobs llamados desde controllers/services | **Eventos + Listeners**: desacopla y facilita auditoría, notificaciones y futuras integraciones. |
| **Storage** | Fotos en disco local (storage/app) o S3; cola para resize | Síncrono en request | **Disco/S3 + cola** para resize/optimización (ProcessPhotosJob). |
| **API vs Web** | Rutas `api.php` (stateless, token) y `web.php` (sesión, CSRF); mismos servicios | Solo web o solo API | **Híbrido**: API para móvil/frontend SPA; Web para dashboard tradicional con Blade o Livewire. |

---

## Decisiones tomadas

- Bounded contexts: Auth, Property, Billing, Messaging, Scoring, Support; con dependencias claras.
- Estructura DDD-lite: Domain (models, enums), Application (casos de uso/application services), Infrastructure (repositorios, adapters), Http (controllers, requests, resources).
- Monolito modular en una sola app Laravel; no paquetes internos por ahora.
- Colas con Redis; eventos para desacoplar y encolar trabajos pesados (emails, fotos, scoring).
- API (stateless) y Web (sesión) comparten lógica de aplicación vía servicios.

---

## Pendientes / Suposiciones

- Asumido Laravel 12 con estructura estándar; si se usa Laravel 11 se mantiene la misma organización de carpetas.
- Health checks y observabilidad (logs, trazas) se detallan en F. DevOps.
- Multi-tenant: por usuario/propiedad; sin `tenant_id` global en esta fase; se puede añadir después en tablas clave.
