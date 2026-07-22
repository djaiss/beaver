---
id: api.authenticate
title: Autenticarse con la API
slug: autenticarse-con-la-api
section: desarrolladores
---

# Autenticarse con la API

Cada solicitud a la API se autentica con un token portador (bearer token). Esta página te lleva de cero a tu primera solicitud exitosa, y luego trata cómo obtener tokens a través de la propia API y cómo revocarlos.

Sustituye `https://kollek.example.com` en los ejemplos por la dirección de tu instancia. La API vive bajo `/api` en esa dirección.

## El camino más rápido: crea una clave en la aplicación

La forma más fácil de conseguir un token es crear una clave de API desde tu perfil.

::::steps
:::step title="Crea una clave de API"
En la aplicación, abre la configuración de tu perfil y ve a **Claves de API**. Crea una clave y ponle una etiqueta que reconozcas más adelante, como "Script de informes".

::screenshot{label="Configuración del perfil, página de claves de API con el formulario de nueva clave"}
:::

:::step title="Copia el token"
El token se muestra una sola vez, justo después de crearlo. Cópialo ahora y guárdalo en un lugar seguro, como un gestor de contraseñas. Si lo pierdes, revoca la clave y crea una nueva.
:::

:::step title="Haz tu primera solicitud"
Envía el token en el encabezado `Authorization`. Una buena primera llamada es `/api/me`, que devuelve tu propio usuario:

```bash
curl https://kollek.example.com/api/me \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```
:::
::::

Si recibes de vuelta un documento JSON que describe a tu usuario, estás autenticado. Crear y revocar claves, y ver cuándo se usó cada una por última vez, se explica en @doc(apiKeys.manage).

:::note
Los tokens no caducan por sí solos. Funcionan hasta que los revocas, así que trata un token como una contraseña.
:::

## Conseguir un token a través de la API

También puedes autenticarte completamente por HTTP, lo cual conviene a scripts e integraciones que gestionan sus propias credenciales.

Inicia sesión con tu correo y contraseña para recibir un token:

```bash
curl -X POST https://kollek.example.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "you@example.com",
    "password": "your-password",
    "device_name": "Reporting script"
  }'
```

La respuesta contiene tu token bajo `data.token`. El campo opcional `device_name` nombra el token para que puedas reconocerlo más adelante en tu lista de claves.

Dos cosas que debes saber:

- Si la @doc(security.twoFactorAuth, "autenticación de dos factores") está activada en tu usuario, el endpoint de inicio de sesión también requiere un campo `code` con un código TOTP actual de tu aplicación de autenticación, o uno de tus @doc(security.recoveryCodes, "códigos de recuperación").
- Registrarte a través de la API también funciona: `POST /api/register` crea un usuario con su propia cuenta y devuelve un token, exactamente igual que registrarse desde el navegador.

Ambos endpoints están limitados a 6 solicitudes por minuto, que es más que suficiente para inicios de sesión reales y detiene los intentos de fuerza bruta.

## Revocar tokens

Tienes dos opciones:

- `DELETE /api/logout` revoca el token que hizo la solicitud. Úsalo cuando un script termina con un token temporal.
- La página **Claves de API** de tu perfil lista todos los tokens y puede revocar cualquiera de ellos. Los endpoints de claves de API en la referencia generada hacen lo mismo por HTTP.

KolleK te envía un correo cuando se crea o elimina una clave desde la aplicación, así que la actividad inesperada de claves no pasa desapercibida. Ver @doc(security.alertEmails).

## A dónde ir ahora

- Aprende las convenciones de las solicitudes en @doc(api.rateLimitsAndConventions).
- Gestiona tus tokens en @doc(apiKeys.manage).
- Explora todos los endpoints en la referencia generada en `/docs/api`.
