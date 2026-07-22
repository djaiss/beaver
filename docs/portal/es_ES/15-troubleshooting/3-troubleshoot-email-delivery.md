---
id: troubleshooting.emailDelivery
title: "Solución de problemas: entrega de correos"
slug: solucion-de-problemas-entrega-de-correos
section: solucion-de-problemas
---

# Solución de problemas: entrega de correos

Invitaste a alguien y no llegó nada. Solicitaste un enlace mágico y tu bandeja de entrada sigue vacía. Esta página explica por qué desaparecen los correos esperados y cómo averiguar qué ha pasado realmente.

## La causa más habitual: una instancia recién creada no envía correos

En una instancia recién autoalojada, el sistema de correo de KolleK, por defecto, **registra los correos en lugar de enviarlos**. Cada correo se redacta y se guarda, pero nada sale del servidor hasta que un operador configura un servicio de correo real.

Esto es intencionado, para que una instancia sin configurar nunca falle en silencio ni envíe spam por accidente. Pero significa que, en una instalación recién creada, las invitaciones, los enlaces mágicos, los restablecimientos de contraseña y las alertas de seguridad parecen desvanecerse todos por igual.

:::note
Si nadie ha configurado aún el correo en tu instancia, no llegará ningún correo, a nadie, nunca. Esto es lo primero que hay que comprobar.
:::

**Si tú operas la instancia**, configura SMTP o Resend siguiendo @doc(selfHosting.setupEmailDelivery).

**Si la opera otra persona**, indícale esa página. No hay nada que puedas cambiar desde dentro de la aplicación.

## Comprueba qué se envió realmente

KolleK registra todos los correos que te envía, con su estado de entrega. Ve a tu perfil y abre tu historial de **correos enviados**. Cada entrada muestra cuándo se envió y, cuando hay seguimiento disponible, si se entregó o rebotó.

Cómo interpretar lo que encuentres:

- **El correo aparece listado y marcado como entregado.** KolleK hizo su trabajo. Revisa tu carpeta de spam y busca en tu bandeja de entrada la dirección remitente.
- **El correo aparece listado y marcado como rebotado.** Tu proveedor de correo lo rechazó. Comprueba que tu dirección es correcta en tu perfil, y si tu proveedor está bloqueando la instancia remitente.
- **El correo aparece listado sin información de entrega.** En instancias que envían por SMTP simple, el seguimiento de entrega no está disponible, así que esto es normal. La ausencia de un rebote es una buena señal.
- **El correo no aparece listado en absoluto.** Nunca se redactó, lo cual normalmente significa que la acción no se completó. Vuelve a intentar la acción.

Todos los detalles de esta pantalla en @doc(activity.logAndSentEmails, "Tu registro personal de actividad y correos enviados").

## Una invitación nunca llegó a la persona invitada

El correo de invitación va a la persona invitada, así que nunca aparece en tu propio historial de enviados. Pide a la persona invitada que revise el spam, verifica que escribiste bien su dirección, y recuerda que las invitaciones caducan a los siete días. Si tienes dudas, envía una nueva. En una instancia recién creada, comprueba primero la configuración del correo, como se explica arriba.

## Las verificaciones, restablecimientos y enlaces mágicos caen en spam

El correo transaccional de una instancia pequeña autoalojada es exactamente lo que despierta las sospechas de los filtros de spam. Marcar un mensaje como "no es spam" suele enseñar a tu proveedor. Los operadores pueden mejorar la capacidad de entrega con una configuración de remitente adecuada, tratada en @doc(selfHosting.setupEmailDelivery).

## A dónde ir a continuación

- Configuración del operador para una entrega real: @doc(selfHosting.setupEmailDelivery).
- Tu historial personal de correo: @doc(activity.logAndSentEmails, "Tu registro personal de actividad y correos enviados").
- Qué es cada correo y cuándo se dispara: @doc(reference.emailsSent).
