---
id: security.index
title: Descripción general de seguridad
slug: seguridad
section: seguridad
---

# Descripción general de seguridad

KolleK guarda registros que te importan: qué tienes, cuánto vale y dónde está. Esta página describe los controles que mantienen a salvo tu usuario y tus datos, para que decidas cuáles activar. Todos son opcionales. La mayoría merecen cinco minutos de tu tiempo.

## Tu contraseña

Toda cuenta empieza con una contraseña. KolleK aplica dos reglas al definirla: debe tener al menos ocho caracteres, y se comprueba contra listas de contraseñas que se sabe que se filtraron en brechas de seguridad anteriores. Si una contraseña que intentas usar es rechazada, es porque apareció en una de esas listas, así que elige algo que no hayas usado en otro sitio.

Puedes cambiar tu contraseña en cualquier momento, y recuperar el acceso si la olvidas. Consulta @doc(auth.resetPassword).

## Autenticación de dos factores

La mejora más importante que puedes hacer. Con la autenticación de dos factores activada, iniciar sesión con tu contraseña también pide un código de seis dígitos de una aplicación de autenticación en tu teléfono. Una contraseña robada por sí sola ya no basta para entrar.

Configúrala en @doc(security.twoFactorAuth), y asegúrate de entender los @doc(security.recoveryCodes, "códigos de recuperación") antes de confiar en ella.

## Códigos de recuperación

Cuando activas la autenticación de dos factores, KolleK te da ocho códigos de recuperación. Cada uno se puede usar una sola vez, en lugar de un código de la aplicación de autenticación, para volver a entrar si pierdes tu teléfono. Guárdalos en un lugar seguro. @doc(security.recoveryCodes) explica cómo.

## Enlaces mágicos

Una forma de iniciar sesión sin contraseña. KolleK te envía por correo un enlace que te inicia sesión directamente, válido durante cinco minutos. Es cómodo, con una contrapartida que conviene entender: un enlace mágico no pide un código de dos factores, porque el acceso a tu bandeja de entrada ya actúa como segundo factor. @doc(auth.magicLinks) explica cuándo usarlos.

## Claves de API

Si usas la API de KolleK, te autenticas con claves de API personales. Se crean y revocan desde tu perfil, y KolleK te envía un correo cada vez que se crea o elimina una, para que una clave que no creaste tú nunca pase desapercibida. Consulta @doc(apiKeys.manage).

## Correos de alerta

KolleK vigila eventos que merece la pena contarte: un intento de inicio de sesión fallido, un inicio de sesión desde un dispositivo nuevo, un cambio en tu dirección IP, una clave de API creada o eliminada. Cuando ocurre uno, recibes un correo. @doc(security.alertEmails) explica qué significa cada alerta y qué hacer al respecto.

## Una configuración sensata

Si solo haces dos cosas, que sean estas:

1. Activa la @doc(security.twoFactorAuth, "autenticación de dos factores").
2. Guarda tus @doc(security.recoveryCodes, "códigos de recuperación") en un lugar que no sea tu teléfono.

Todo lo demás en esta sección puede esperar hasta que lo necesites.

## Páginas de esta sección

1. @doc(security.twoFactorAuth)
2. @doc(security.recoveryCodes)
3. @doc(auth.magicLinks)
4. @doc(auth.resetPassword)
5. @doc(security.alertEmails)
6. @doc(apiKeys.manage)
