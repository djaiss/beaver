---
id: troubleshooting.signIn
title: "Solución de problemas: inicio de sesión"
slug: solucion-de-problemas-inicio-de-sesion
section: solucion-de-problemas
---

# Solución de problemas: inicio de sesión

¿Te has quedado bloqueado, o algo en la pantalla de inicio de sesión no hace lo que esperabas? Busca tu síntoma a continuación. Cada entrada da primero la solución y después enlaza a la explicación completa.

## He olvidado mi contraseña

Usa el enlace **he olvidado mi contraseña** en la pantalla de inicio de sesión. Introduce tu correo, abre el correo de restablecimiento y elige una contraseña nueva. El enlace de restablecimiento caduca a los 60 minutos, así que úsalo cuanto antes, y solicita otro si caduca.

Alternativa más rápida: solicita un @doc(auth.magicLinks, "enlace mágico") en su lugar. Te inicia sesión sin contraseña, y puedes definir una contraseña nueva después desde tu perfil.

Todos los detalles en @doc(auth.resetPassword).

## Mi contraseña nueva se rechaza una y otra vez

KolleK requiere al menos ocho caracteres y rechaza cualquier contraseña que haya aparecido en una filtración de datos pública. El rechazo tiene que ver con la contraseña en sí, no con tu cuenta. Elige algo más largo y único que no hayas usado en otro sitio. Consulta @doc(auth.resetPassword).

## He perdido mi dispositivo de doble factor

En el desafío de doble factor, introduce uno de tus **códigos de recuperación** en lugar del código de seis dígitos. Cada código de recuperación funciona una sola vez. Una vez dentro, desactiva y vuelve a activar la autenticación de dos factores con tu nuevo dispositivo para obtener un emparejamiento nuevo y un conjunto nuevo de códigos.

Todos los detalles en @doc(security.recoveryCodes).

:::warning
Si has perdido tu autenticador y no tienes códigos de recuperación, no hay forma de completar el paso de doble factor por ti mismo. En una instancia autoalojada, habla con quien opera tu servidor.
:::

## Mi enlace mágico no funciona

Los enlaces mágicos son válidos durante **cinco minutos** y funcionan **una sola vez**. Si el tuyo caducó o ya se usó, solicita uno nuevo desde la pantalla de inicio de sesión. Asegúrate de abrir el enlace en el dispositivo en el que quieres iniciar sesión.

Todos los detalles en @doc(auth.magicLinks).

## Lo he intentado demasiadas veces y ahora estoy bloqueado

Los intentos repetidos y rápidos se limitan para frenar los intentos de adivinar contraseñas. Espera un minuto e inténtalo de nuevo, con calma. Si no estás seguro de la contraseña, cambia al @doc(auth.resetPassword, "flujo de restablecimiento") o a un @doc(auth.magicLinks, "enlace mágico") en lugar de seguir probando.

## Me llegó un correo de "inicio de sesión fallido" que no reconozco

Alguien introdujo tu correo con una contraseña incorrecta. Consulta @doc(security.alertEmails) para saber qué significa y cuándo actuar.

## El enlace de mi invitación no funciona

Dos causas habituales:

- **La invitación caducó.** Las invitaciones duran siete días. Pide al propietario de la cuenta que envíe una nueva.
- **Tu correo ya tiene un usuario en KolleK.** Una persona pertenece a exactamente una cuenta, así que un correo que ya tiene su propia cuenta no puede aceptar una invitación.

Todos los detalles en @doc(collaboration.invitePeople).

## El correo que estoy esperando nunca llega

Puede que el correo de restablecimiento, el enlace mágico o la invitación no te estén llegando. Eso suele ser un problema de entrega, no de inicio de sesión. Consulta @doc(troubleshooting.emailDelivery).

## A dónde ir a continuación

- Lo básico de cada forma de iniciar sesión: @doc(auth.signIn).
- Refuerza la seguridad una vez que hayas vuelto a entrar: @doc(security.index).
