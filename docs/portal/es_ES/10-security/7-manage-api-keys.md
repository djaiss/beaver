---
id: apiKeys.manage
title: Gestiona claves de API
slug: gestiona-claves-de-api
section: seguridad
---

# Gestiona claves de API

Una clave de API es un token personal que permite que un script o una aplicación actúe como tú a través de la API de KolleK. Esta página cubre el ciclo de vida: crear una clave, hacerle seguimiento y revocarla. Lo que puedes hacer realmente con una clave se explica en la @doc(api.authenticate, "sección para desarrolladores").

Si nunca piensas usar la API, puedes saltarte esta página por completo. No existe ninguna clave hasta que creas una.

## Crea una clave

::::steps
:::step title="Abre tus ajustes de claves de API"
Ve a tu perfil y abre el área de claves de API. Verás las claves que ya tengas, cada una con la fecha de su último uso.
:::

:::step title="Nombra la clave nueva"
Elige crear una clave y dale una **etiqueta** que diga para qué es, como "Script de importación" o "Panel doméstico". Las etiquetas son para tu yo futuro, que decidirá qué clave es segura revocar.
:::

:::step title="Copia el token de inmediato"
KolleK muestra el token una sola vez, justo después de crearlo. Cópialo ahora y guárdalo en algún lugar seguro, como un gestor de contraseñas.

::screenshot{label="Clave de API nueva con el token revelado una sola vez"}
:::
::::

:::warning
El token se muestra solo una vez. Si lo pierdes, no puedes volver a verlo. Revoca la clave y crea una nueva.
:::

KolleK te envía un aviso por correo cada vez que se crea una clave en tu usuario, para que una clave inesperada nunca pase desapercibida.

## Haz seguimiento de tus claves

El área de claves de API enumera cada clave con su etiqueta y cuándo se usó por última vez. Esa última hora de uso es tu aliada: una clave que no se ha usado en meses es probablemente una clave que puedes revocar, y una clave usada hace cinco minutos cuando tu script no se ha ejecutado es una clave que investigar.

Un hábito lo mantiene manejable: una clave por propósito. Cuando cada integración tiene su propia clave, puedes revocar una sin romper las demás.

## Revoca una clave

Elimina la clave de la misma lista. Cualquier cosa que todavía use su token deja de funcionar de inmediato, y KolleK te envía un aviso por correo de la eliminación.

Revoca una clave cuando:

- Ya no uses el script o la aplicación a la que pertenecía.
- El token pueda haberse filtrado, por ejemplo si se subió a un repositorio o se compartió en un chat.
- Recibas una @doc(security.alertEmails, "alerta de clave creada o eliminada") que no reconozcas. En ese caso, cambia también tu contraseña.

:::note
Iniciar sesión a través de la API también crea un token entre bastidores. Esos tokens de inicio de sesión no activan el correo de clave creada, así que las alertas que recibes siguen siendo significativas.
:::

## A dónde ir después

- Pon una clave en uso con tu primera solicitud: @doc(api.authenticate).
- Entiende los correos relacionados con las claves: @doc(security.alertEmails).
