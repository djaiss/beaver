---
id: reference.emailsSent
title: Correos que envía KolleK
slug: correos-que-envia-kollek
section: referencia
---

# Correos que envía KolleK

Todos los correos que el sistema puede enviar, qué los activa y quién los recibe. Usa esta página para reconocer un mensaje legítimo, o para verificar la entrega cuando gestionas una instancia.

KolleK guarda un registro de cada correo que te envía, incluyendo el estado de entrega y de rebote, en tu @doc(activity.logAndSentEmails, "página de correos enviados"). Los operadores que aún no han configurado un servidor de correo deberían leer @doc(selfHosting.setupEmailDelivery), porque una instancia recién instalada solo registra el correo y no envía nada.

## Entrar y mantener el acceso

| Correo | Se activa cuando | Se envía a |
| --- | --- | --- |
| Invitación a la cuenta | Un propietario invita a alguien a la cuenta. El enlace de invitación caduca a los siete días. | La dirección invitada |
| Enlace mágico | Alguien solicita un enlace de inicio de sesión sin contraseña. El enlace es válido durante cinco minutos. | El correo de la cuenta |
| Verificación de correo | Te registras, o cambias tu dirección de correo electrónico. | La nueva dirección |
| Restablecimiento de contraseña | Usas el enlace de contraseña olvidada. El enlace de restablecimiento es válido durante 60 minutos. | El correo de la cuenta |

## Alertas de seguridad

Estas llegan sin que las solicites cuando ocurre algo destacable en tu cuenta. Consulta @doc(security.alertEmails) para saber qué hacer si una te sorprende.

| Correo | Se activa cuando | Se envía a |
| --- | --- | --- |
| Alerta de inicio de sesión fallido | Falla un intento de inicio de sesión con contraseña en una cuenta existente. | El correo de la cuenta |
| Alerta de nuevo inicio de sesión | Ocurre un inicio de sesión exitoso, indicando el dispositivo usado. | El correo de la cuenta |
| Alerta de cambio de dirección IP | Un inicio de sesión llega desde una dirección IP distinta a la de la última vez. | El correo de la cuenta |
| Clave de API creada | Creas una clave de API manualmente. Los tokens creados al iniciar sesión a través de la API no activan este aviso. | El correo de la cuenta |
| Clave de API eliminada | Eliminas una clave de API. | El correo de la cuenta |

## Avisos para el operador

Estos van a la dirección del operador configurada en la instancia, no a los coleccionistas. Existen para que quien gestiona el servidor sepa cuándo las personas se marchan.

| Correo | Se activa cuando | Se envía a |
| --- | --- | --- |
| Usuario eliminado | Una persona elimina su propio usuario, incluyendo el motivo que dio. | La dirección del operador |
| Usuario eliminado automáticamente | El sistema elimina a un usuario que optó por la eliminación por inactividad y ha estado inactivo durante seis meses. | La dirección del operador |

## A dónde ir ahora

- Reconoce y reacciona ante las alertas: @doc(security.alertEmails).
- Haz que el correo realmente se envíe en tu instancia: @doc(selfHosting.setupEmailDelivery).
- Comprueba qué se te ha enviado: @doc(activity.logAndSentEmails, "Tu registro de actividad personal y correos enviados").
