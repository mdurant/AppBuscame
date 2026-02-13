# A. Alcance y UX

## Flujos UX paso a paso

### 1. Registro → Verificación email → Código 6 dígitos → Login/Dashboard

| Paso | Pantalla/Acción | Validaciones | Errores/Mensajes |
|------|------------------|--------------|------------------|
| 1.1 | **Registro**: formulario con nombres, apellidos, email, password, repetir password. Botón "Registrarme". | Email único, password mínimo 8 caracteres (complejidad), passwords coinciden. | "El correo ya está registrado", "Las contraseñas no coinciden", "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial." |
| 1.2 | Mensaje: "Revisa tu correo. Te enviamos un enlace para verificar tu cuenta." + opción "Reenviar enlace" (con cooldown 60s). | Rate limit en reenvío. | "Espera 60 segundos para reenviar." |
| 1.3 | Usuario hace clic en enlace del email → **Verificación email**: pantalla "Correo verificado. Introduce el código de 6 dígitos que te enviamos." Campo numérico 6 dígitos, botón "Verificar", enlace "No recibí el código - Renovar" (si no expiró no muestra renovar hasta cerca del límite). | Código numérico 6 dígitos, no expirado (15 min). | "Código inválido o expirado", "El código ha expirado. Solicita uno nuevo." |
| 1.4 | Si código OK → redirección a **Login** (o auto-login) y luego **Dashboard**. | Sesión válida. | "Sesión iniciada correctamente." |

**Estados intermedios:** `registered_pending_email`, `email_verified_pending_otp`, `fully_verified`.

---

### 2. Activación 2FA + envío de backup codes

| Paso | Pantalla/Acción | Validaciones | Errores/Mensajes |
|------|------------------|--------------|------------------|
| 2.1 | **Dashboard → Seguridad**: opción "Activar autenticación en dos pasos (2FA)". | Usuario autenticado, 2FA no activo. | — |
| 2.2 | Pantalla "Escanea el código QR con tu app" (Google Authenticator, Authy, etc.) + código manual (secret) por si no puede escanear. Botón "Continuar". | — | "Guarda los códigos de respaldo en un lugar seguro." |
| 2.3 | Pantalla "Introduce el código de 6 dígitos de tu app para confirmar". Campo + "Verificar". | Código TOTP válido. | "Código incorrecto. Intenta de nuevo." |
| 2.4 | Se muestran **códigos de respaldo** (ej. 8 códigos de un solo uso). Mensaje: "Te hemos enviado estos códigos también por correo. Guárdalos en un lugar seguro." Botón "He guardado los códigos". | — | — |
| 2.5 | Vuelta a Seguridad: "2FA activado". En próximos logins se pedirá código TOTP o backup. | — | — |

**Renovación de backup codes:** Misma sección, "Generar nuevos códigos de respaldo" → confirma contraseña → nuevos códigos + email; los anteriores se invalidan.

---

### 3. Publicación: draft → completitud → pago suscripción → published

| Paso | Pantalla/Acción | Validaciones | Errores/Mensajes |
|------|------------------|--------------|------------------|
| 3.1 | **Dashboard → Publicar aviso**. Formulario por pasos o secciones: tipo propiedad, tiempo/días disponibles, costo, hora llegada/salida, incluye aseo (sí/no), dirección. | Campos obligatorios por sección. | Mensajes de campo requerido por campo. |
| 3.2 | **Dirección**: input con autocompletado (Google Places). Al elegir dirección se muestra **mapa en tiempo real** y se persisten lat/lng. | Dirección válida, coordenadas obtenidas. | "No pudimos validar la dirección. Inténtalo de nuevo." |
| 3.3 | **Fotos**: subir hasta 5. Vista previa, orden opcional. Barra de **% completitud** visible (ej. 60%). | Máximo 5 imágenes, formatos permitidos, tamaño máx. | "Máximo 5 fotos.", "Formato no permitido." |
| 3.4 | Al guardar sin publicar → estado **draft**. Botón "Publicar" visible cuando completitud ≥ 100%. | Completitud 100%, dirección y fotos OK. | "Completa todos los campos obligatorios y sube al menos una foto para publicar." |
| 3.5 | Al pulsar "Publicar" → si no tiene suscripción activa: redirección a **pago/suscripción**. Estado **pending_payment**. | Suscripción activa o pago pendiente. | "Para publicar necesitas una suscripción activa." |
| 3.6 | Tras pago exitoso → estado **published**. Aviso visible en listados. Opciones: **Pausar** (paused), **Archivar** (archived). | — | "Tu aviso ha sido publicado." |

**Estados:** `draft` → `pending_payment` → `published` | `paused` | `archived`.

---

### 4. Mensajería: creación thread, mensajes, estados, auditoría

| Paso | Pantalla/Acción | Validaciones | Errores/Mensajes |
|------|------------------|--------------|------------------|
| 4.1 | Desde un aviso (arrendatario) o desde listado (ofertante): "Enviar mensaje" / "Contactar". Se crea **thread** entre arrendatario y ofertante (por propiedad). | Un thread por par (usuario, propiedad) o por (usuario A, usuario B, propiedad). | — |
| 4.2 | **Vista thread**: lista de mensajes ordenados por fecha. Campo de texto + "Enviar". Estados visibles (ej. "Enviado", "Visto"). | Mensaje no vacío, longitud máx. | "El mensaje no puede estar vacío." |
| 4.3 | **Estados y trazabilidad**: cada mensaje tiene estado (enviado, entregado, leído). Cambios registrados en auditoría (quién, cuándo). Nada se elimina; solo "ocultar" si se implementa, sin borrar de BD. | Solo participantes del thread pueden ver/escribir. | "No tienes permiso para ver esta conversación." |
| 4.4 | **Canal de ayuda**: desde menú, "Ayuda" → FAQ, "Contactar soporte" (crear ticket), base de conocimiento. Tickets con estado (abierto, en curso, resuelto). | — | — |

---

## Wireflow textual (pantallas, acciones, validaciones, errores)

### Pantallas principales

- **Pública:** Home, Listado de propiedades, Detalle propiedad, Login, Registro.
- **Post-login:** Dashboard (resumen), Perfil, Seguridad (sesiones, cambio contraseña, 2FA), Publicar aviso (wizard), Mis avisos, Historial de pagos/arriendos, Mensajes (lista + thread), Ayuda (FAQ, tickets, base de conocimiento).

### Acciones globales

- Validación en tiempo real donde aplique (email formato, fuerza de contraseña, código 6 dígitos).
- CSRF en todos los formularios web.
- Mensajes de error amigables (sin exponer detalles técnicos al usuario).
- Rate limiting en registro, reenvío de email, OTP y login para evitar enumeración y abuso.

### Resumen de mensajes amigables

- Registro: "Cuenta creada. Revisa tu correo para verificar tu cuenta."
- Email verificado: "Correo verificado. Introduce el código que te enviamos."
- OTP: "Código correcto. Redirigiendo al panel."
- 2FA: "Autenticación en dos pasos activada. Hemos enviado tus códigos de respaldo por correo."
- Publicación: "Aviso guardado como borrador." / "Para publicar necesitas activar una suscripción." / "Aviso publicado correctamente."
- Mensajes: "Mensaje enviado." / "No puedes eliminar mensajes; la conversación queda registrada."

---

## Decisiones tomadas

- **Verificación en dos pasos (email + OTP):** Se exige verificación de email primero y luego OTP de 6 dígitos (15 min, renovable) para elevar la seguridad y reducir cuentas falsas.
- **2FA opcional pero recomendado:** Activación desde Seguridad; backup codes por email para no perder acceso.
- **Publicación condicionada a pago:** Sin suscripción activa no se puede pasar a `published`; estado `pending_payment` hasta pagar.
- **Mensajería sin borrado:** Solo estados y auditoría; no soft delete de mensajes (o solo "ocultar" en UI sin borrar en BD).
- **Completitud %:** Cálculo basado en campos obligatorios + fotos + dirección para guiar al usuario antes de publicar.

---

## Pendientes / Suposiciones

- **Multi-tenant:** Por ahora un usuario puede tener varias propiedades; no se asume organización/empresa como tenant; se puede extender después.
- **Idioma:** Interfaz y mensajes en español.
- **Pago:** Se asume integración con un proveedor (Stripe, Mercado Pago, etc.); flujo UX descrito; implementación concreta en Fase E.
- **Mapas:** Google Places + Maps para autocompletar y mostrar mapa; clave API y términos de uso por cuenta.
- **Soft delete:** No usado para mensajes/auditoría; si se añade "ocultar", será solo flag en UI sin eliminar registros.
