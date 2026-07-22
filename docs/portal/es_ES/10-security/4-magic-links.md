---
id: auth.magicLinks
title: Los enlaces mágicos explicados
slug: enlaces-magicos
section: seguridad
---

# Los enlaces mágicos explicados

Un enlace mágico es una forma de iniciar sesión sin contraseña. En lugar de escribir tu contraseña, le pides a KolleK que te envíe un enlace por correo. Abre el enlace, y quedas conectado. Esta página explica cómo funciona, cuándo resulta cómodo, y la única contrapartida que deberías entender antes de confiar en él.

## Pide un enlace mágico

En la página de inicio de sesión, elige la opción de enlace mágico, introduce tu **correo electrónico** y envíalo. KolleK envía un enlace de un solo uso a esa dirección. Ábrelo, y llegas a tu panel.

Por tu privacidad, la página muestra la misma confirmación exista o no una cuenta para la dirección que introdujiste, así que nunca revela quién está registrado.

## Las reglas que sigue

- **El enlace es válido durante cinco minutos.** Si caduca antes de que lo abras, pide otro. No se pierde nada.
- **Va solo al correo de tu cuenta.** Necesitas acceso a esa bandeja de entrada. Esto también es lo que hace seguro al enlace: solo quien pueda leer tu correo puede usarlo.
- **Funciona una vez.** Un enlace que ya te ha iniciado sesión no se puede reutilizar.

## La contrapartida con la autenticación de dos factores

Iniciar sesión con un enlace mágico no pide un código de @doc(security.twoFactorAuth, "dos factores").

Esto es deliberado, no un descuido. Un enlace mágico ya demuestra dos cosas a la vez: que quien inicia sesión conoce tu dirección de correo, y que controla la bandeja de entrada detrás de ella. La bandeja de entrada actúa como segundo factor.

:::warning
Si usas la autenticación de dos factores, recuerda que cualquiera que controle tu bandeja de entrada de correo puede iniciar sesión en KolleK con un enlace mágico, sin llegar a ver nunca tu autenticador. Tu cuenta de correo es la puerta real, así que protégela con una contraseña fuerte y su propia configuración de dos factores.
:::

## Cuándo usarlo

Los enlaces mágicos te convienen cuando:

- Estás en un dispositivo donde no quieres escribir tu contraseña.
- Has olvidado tu contraseña y solo necesitas entrar. Una vez dentro, puedes @doc(auth.resetPassword, "establecer una contraseña nueva") desde tu perfil.
- Prefieres no usar una contraseña en el día a día y tu cuenta de correo está bien protegida.

Prefiere tu contraseña y tu código de autenticador cuando estés en una máquina compartida o poco fiable en la que preferirías no abrir tu bandeja de entrada en absoluto.

## A dónde ir después

- Todas las vías de inicio de sesión en un solo lugar: @doc(auth.signIn).
- Refuerza la puerta principal: @doc(security.twoFactorAuth).
- ¿El enlace nunca llegó? Consulta @doc(troubleshooting.emailDelivery).
