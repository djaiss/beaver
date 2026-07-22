---
id: auth.resetPassword
title: Restablece tu contraseña
slug: restablece-tu-contrasena
section: seguridad
---

# Restablece tu contraseña

Tanto si has olvidado tu contraseña como si simplemente quieres una nueva, esta página cubre ambas vías: recuperar el acceso desde la página de inicio de sesión, y cambiar tu contraseña de forma deliberada desde tu perfil.

## Si has olvidado tu contraseña

1. En la página de inicio de sesión, elige el enlace **olvidé mi contraseña**.
2. Introduce tu dirección de correo electrónico y envíala.
3. Abre el correo que te envía KolleK y sigue el enlace de restablecimiento.
4. Elige una contraseña nueva y confírmala. Ya puedes iniciar sesión con ella.

Dos comportamientos merece la pena conocer para que no te confundan:

- **El mensaje de confirmación siempre es el mismo**, exista o no una cuenta para la dirección que escribiste. Esto protege tu privacidad al no revelar nunca quién está registrado. Si tienes una cuenta, el correo llegará.
- **El enlace de restablecimiento caduca a los 60 minutos.** Si lo abres demasiado tarde, simplemente pide otro.

:::note
Si prefieres saltarte el restablecimiento por completo, un @doc(auth.magicLinks, "enlace mágico") puede iniciarte sesión sin contraseña. Una vez dentro, puedes establecer una contraseña nueva desde tu perfil.
:::

## Si solo quieres cambiarla

No necesitas el flujo de contraseña olvidada para renovar tu contraseña. Ve a tu perfil, abre el área de seguridad, y cambia tu contraseña allí. Introducirás tu contraseña actual y elegirás la nueva.

## Por qué se puede rechazar una contraseña

KolleK comprueba toda contraseña nueva contra dos reglas, así que un rechazo nunca es un misterio:

- **Al menos ocho caracteres.** Las contraseñas más cortas se rechazan directamente.
- **Ninguna contraseña filtrada conocida.** Tu contraseña candidata se comprueba contra listas de contraseñas que han aparecido en filtraciones públicas de datos. Si se ha filtrado alguna vez en algún sitio, se rechaza, aunque parezca fuerte. Esto tiene que ver con la contraseña en sí, no con tu cuenta, así que elige algo que no hayas usado en otros sitios.

Un gestor de contraseñas evita ambas reglas sin esfuerzo generando algo largo y único.

## A dónde ir después

- Añade un segundo paso para que una contraseña robada no baste: @doc(security.twoFactorAuth).
- ¿Sigues sin poder entrar? Revisa @doc(troubleshooting.signIn).
- ¿El correo de restablecimiento nunca llegó? Consulta @doc(troubleshooting.emailDelivery).
