---
id: security.alertEmails
title: Correos de alerta de inicio de sesión y seguridad
slug: correos-de-alerta-de-seguridad
section: seguridad
---

# Correos de alerta de inicio de sesión y seguridad

De vez en cuando, KolleK puede enviarte un correo sin que hayas pedido nada. Estas alertas existen para asegurarse de que, cuando pasa algo en torno a tu usuario, te enteres por KolleK antes que por cualquier otra vía. Esta página enumera cada alerta, qué significa y qué hacer si alguna te sorprende.

## Intento de inicio de sesión fallido

**Cuándo llega:** alguien introdujo tu correo con una contraseña incorrecta en la página de inicio de sesión.

**Si fuiste tú**, al escribir mal tu propia contraseña, ignóralo.

**Si no fuiste tú**, alguien está probando con tu dirección. Un intento fallido suele ser solo ruido, pero alertas repetidas significan que tu correo está siendo objetivo de alguien. Asegúrate de que tu contraseña es exclusiva de KolleK, y activa la @doc(security.twoFactorAuth, "autenticación de dos factores") para que una contraseña adivinada no baste.

## Nuevo inicio de sesión

**Cuándo llega:** ocurrió un inicio de sesión con éxito, y el correo indica el dispositivo desde el que vino.

**Si fuiste tú**, en un navegador, teléfono u ordenador nuevo, ignóralo.

**Si no fuiste tú**, alguien tiene tu contraseña. @doc(auth.resetPassword, "Cambia tu contraseña") de inmediato, y revisa tu cuenta por si hay algo inesperado.

## Cambio de dirección IP

**Cuándo llega:** iniciaste sesión desde una dirección de red distinta a la de la última vez.

Es normal cuando viajas, cambias de red, o tu proveedor rota direcciones. Solo merece atención si llega junto a un inicio de sesión que no reconoces.

## Clave de API creada, clave de API eliminada

**Cuándo llega:** se creó o revocó una @doc(apiKeys.manage, "clave de API") en tu usuario.

**Si fuiste tú**, gestionando tus claves, ignóralo.

**Si no fuiste tú**, tómatelo en serio. Una clave inesperada significa que alguien tenía suficiente acceso para crear una. Revoca la clave, cambia tu contraseña, y revisa tus claves restantes y su última hora de uso.

:::note
Los tokens de inicio de sesión creados cuando entras a través de la API no activan el correo de clave creada. Solo lo hacen las claves que creas a mano, para que la alerta siga siendo significativa.
:::

## Correos que tú pediste

Otros dos correos llegan solo porque alguien los solicitó, así que no son alertas en sí mismas: el correo de @doc(auth.magicLinks, "enlace mágico"), y el correo de restablecimiento de contraseña. Si recibes uno que no solicitaste, alguien introdujo tu dirección en ese formulario. Ninguno de los dos se puede usar sin acceso a tu bandeja de entrada, pero correos repetidos que no solicitaste son otra señal de que alguien está probando con tu dirección.

## Si algo realmente parece ir mal

1. @doc(auth.resetPassword, "Cambia tu contraseña").
2. Activa la @doc(security.twoFactorAuth, "autenticación de dos factores") si está desactivada.
3. Revisa tus @doc(apiKeys.manage, "claves de API") y revoca cualquiera que no reconozcas.
4. Consulta @doc(activity.logAndSentEmails, "tu registro de actividad personal") por si hay acciones que no realizaste.

## A dónde ir después

- Consulta todo lo que KolleK te ha enviado alguna vez, con su estado de entrega: @doc(activity.logAndSentEmails, "Tu registro de actividad personal y los correos enviados").
- El catálogo completo de cada correo que KolleK puede enviar: @doc(reference.emailsSent).
