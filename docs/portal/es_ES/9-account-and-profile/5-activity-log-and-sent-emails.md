---
id: activity.logAndSentEmails
title: Tu registro de actividad y los correos enviados
slug: tu-registro-de-actividad-y-los-correos-enviados
section: cuenta-y-perfil
---

# Tu registro de actividad y los correos enviados

KolleK guarda dos registros sobre ti que puedes consultar en cualquier momento: todo lo que has hecho, y todos los correos que el sistema te ha enviado. Ambos viven en el área de tu perfil, y ambos existen por la misma razón, la transparencia. Cuando te preguntas "¿de verdad cambié eso?" o "¿de verdad se envió ese correo de enlace mágico?", la respuesta está aquí.

## Tu registro de actividad

El @doc(activity.feedAndAuditTrail, "registro de actividad") que recorre toda la cuenta tiene una vista personal: un historial completo de tus propias acciones, desde crear un elemento hasta cambiar un ajuste. Ábrelo desde el área de tu perfil.

Úsalo para reconstruir tus pasos. Si la ubicación de un ejemplar parece incorrecta, tu registro mostrará si tú lo moviste, y cuándo.

## Tus correos enviados

KolleK registra todos los correos que te envía: enlaces mágicos, invitaciones que recibiste, mensajes de verificación y @doc(security.alertEmails, "alertas de seguridad"). El área de tu perfil los enumera, los más recientes primero, diez por página.

Cada entrada muestra qué se envió y cuándo. Cuando el servicio de correo de la instancia lo notifica, también verás si el mensaje se entregó, o si rebotó.

Esta lista es la forma más rápida de solucionar un correo que falta:

- **El correo aparece aquí pero nunca llegó a tu bandeja de entrada.** Revisa tu carpeta de correo no deseado, y comprueba si la entrada muestra un rebote.
- **El correo no aparece aquí en absoluto.** La acción que debería haberlo activado no ocurrió, así que solicítalo de nuevo.
- **Los correos aparecen aquí pero nunca se entregan.** En una instancia autoalojada esto normalmente significa que la entrega de correo aún no está configurada. Dirige a tu administrador a @doc(selfHosting.setupEmailDelivery, "configurar la entrega de correo").

:::note
Esta página muestra los correos enviados a ti. Es personal, como el resto de tu perfil, y los demás miembros no pueden explorar tu lista.
:::

## A dónde ir después

- Entiende el historial de toda la cuenta en @doc(activity.feedAndAuditTrail).
- ¿Te falta un correo esperado? Revisa @doc(troubleshooting.emailDelivery).
