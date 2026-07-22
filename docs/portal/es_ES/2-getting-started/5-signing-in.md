---
id: auth.signIn
title: Iniciar sesión
slug: iniciar-sesion
section: primeros-pasos
---

# Iniciar sesión

KolleK te ofrece varias formas de iniciar sesión. Esta página cubre cada una para que elijas la que más te convenga, y te indica adónde ir si te quedas fuera de tu cuenta.

## Iniciar sesión con correo y contraseña

La forma habitual. Ve a la página de inicio de sesión, introduce el **correo electrónico** y la **contraseña** con los que te registraste, y envía el formulario. Llegarás a tu panel.

Si la @doc(security.twoFactorAuth, "autenticación de dos factores") está activada en tu cuenta, se te pedirá un código justo después de tu contraseña. Ver más abajo.

## Iniciar sesión con un enlace mágico

Si prefieres no escribir una contraseña, KolleK puede enviarte por correo un enlace que inicia tu sesión.

En la página de inicio de sesión, elige la opción de enlace mágico, introduce tu **correo electrónico** y envía el formulario. KolleK envía un enlace de un solo uso a esa dirección. Ábrelo, y tu sesión quedará iniciada.

Dos cosas que debes saber:

- **El enlace es válido durante cinco minutos.** Si caduca, simplemente solicita otro.
- **El enlace llega al correo de tu cuenta**, así que necesitas acceso a esa bandeja de entrada. Esto es también lo que lo mantiene seguro: solo quien puede leer tu correo puede usarlo.

## El paso de dos factores

Si has activado la autenticación de dos factores, iniciar sesión con tu contraseña requiere un paso adicional. Después de que se acepte tu contraseña, KolleK te pide el código actual de tu aplicación de autenticación. Introdúcelo para terminar de iniciar sesión.

Si no puedes acceder a tu aplicación de autenticación, puedes introducir uno de tus @doc(security.recoveryCodes, "códigos de recuperación") en su lugar. Cada código de recuperación funciona una sola vez.

:::warning
Iniciar sesión con un enlace mágico no pide un código de dos factores, porque el acceso a tu bandeja de correo ya actúa como segundo factor. Si confías en la autenticación de dos factores, ten esto en cuenta al elegir cómo iniciar sesión, y protege tu cuenta de correo en consecuencia.
:::

Configurar la autenticación de dos factores y guardar códigos de recuperación se explica en la sección **Seguridad** de esta documentación.

## Olvidaste tu contraseña

Si no recuerdas tu contraseña, usa el enlace "olvidé mi contraseña" en la página de inicio de sesión. Introduce tu correo, y KolleK te envía un enlace de restablecimiento.

Por tu privacidad, KolleK siempre muestra el mismo mensaje de confirmación exista o no una cuenta para esa dirección, así que la página no revelará quién está registrado. Si tienes una cuenta, el correo de restablecimiento llegará. Si usas un enlace mágico para volver a entrar, puedes restablecer tu contraseña después desde tu perfil.

## A dónde ir ahora

- ¿Eres nuevo aquí y sigues configurando todo? Vuelve a @doc(gettingStarted.checklist).
- ¿Quieres una protección más fuerte? Activa la autenticación de dos factores desde la sección **Seguridad**.
