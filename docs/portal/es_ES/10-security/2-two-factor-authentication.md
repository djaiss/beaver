---
id: security.twoFactorAuth
title: Protege tu cuenta con autenticación de dos factores
slug: autenticacion-de-dos-factores
section: seguridad
---

# Protege tu cuenta con autenticación de dos factores

La autenticación de dos factores añade un segundo paso al inicio de sesión. Después de que se acepte tu contraseña, KolleK pide un código de seis dígitos de una aplicación de autenticación en tu teléfono. Incluso si alguien descubre tu contraseña, no puede entrar sin ese código.

Es el control de seguridad más eficaz que ofrece KolleK, y configurarlo lleva unos minutos.

## Lo que necesitarás

Una aplicación de autenticación en tu teléfono, cualquiera que admita códigos de un solo uso basados en tiempo. Si alguna vez escaneaste un código QR para proteger otra cuenta, ya tienes una.

## Actívala

::::steps
:::step title="Abre tus ajustes de seguridad"
Ve a tu perfil y abre el área de seguridad, luego elige configurar la **autenticación de dos factores**.
:::

:::step title="Escanea el código QR"
KolleK muestra un código QR. Abre tu aplicación de autenticación, añade una cuenta nueva y escanea el código. La aplicación empieza a mostrar un código de seis dígitos para KolleK que cambia cada 30 segundos.

::screenshot{label="Pantalla de configuración de dos factores con el código QR"}
:::

:::step title="Confirma con un código"
Escribe el código de seis dígitos actual de tu aplicación en el campo de confirmación y envíalo. Esto demuestra que la aplicación y KolleK están sincronizados antes de que cambie nada en la forma en que inicias sesión.
:::

:::step title="Guarda tus códigos de recuperación"
KolleK genera ocho códigos de recuperación. Cópialos en algún lugar seguro que no sea tu teléfono, como un gestor de contraseñas o una hoja impresa. Cada código puede iniciarte sesión una vez si alguna vez pierdes tu autenticador.

::screenshot{label="Los ocho códigos de recuperación mostrados tras la configuración"}
:::
::::

:::warning
Si pierdes tu autenticador y no tienes códigos de recuperación, no puedes completar el paso de dos factores, y puedes quedarte bloqueado fuera de tu usuario. Guarda los códigos antes de cerrar la página.
:::

## Lo que cambia al iniciar sesión

A partir de ahora, iniciar sesión con tu correo y contraseña lleva un paso adicional. Después de que se acepte tu contraseña, KolleK pide el código actual de tu aplicación de autenticación. Introdúcelo y entras.

Si no puedes acceder a tu aplicación, introduce en su lugar uno de tus @doc(security.recoveryCodes, "códigos de recuperación").

:::note
Iniciar sesión con un @doc(auth.magicLinks, "enlace mágico") no pide un código de dos factores. El acceso a tu bandeja de entrada de correo ya actúa como segundo factor, así que protege esa bandeja en consecuencia.
:::

## Desactívala

Puedes desactivar la autenticación de dos factores desde la misma área de seguridad. Hacerlo elimina el paso del código al iniciar sesión y también elimina tus códigos de recuperación y el emparejamiento con tu aplicación de autenticación. Si la vuelves a activar más tarde, escanearás un código QR nuevo y recibirás un conjunto nuevo de códigos de recuperación.

## A dónde ir después

- Asegúrate de que tu respaldo funciona: @doc(security.recoveryCodes).
- Entiende la vía sin contraseña y su contrapartida: @doc(auth.magicLinks).
- Consulta todas las formas de entrar en la aplicación: @doc(auth.signIn).
