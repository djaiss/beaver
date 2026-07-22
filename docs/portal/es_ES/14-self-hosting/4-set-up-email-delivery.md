---
id: selfHosting.setupEmailDelivery
title: Configura la entrega de correo
slug: configura-la-entrega-de-correo
section: alojamiento-propio
---

# Configura la entrega de correo

El correo es la forma en que KolleK llega a las personas fuera de una sesión de navegador: las @doc(collaboration.invitePeople, "invitaciones"), los @doc(auth.magicLinks, "enlaces mágicos"), los restablecimientos de contraseña, la verificación de correo y las @doc(security.alertEmails, "alertas de seguridad") llegan todos por correo. Hasta que configures la entrega, ninguno llega a ningún sitio.

## Por defecto no se envía nada

Una instancia recién instalada viene con `MAIL_MAILER=log`. Cada correo se escribe en el archivo de log de la aplicación en lugar de enviarse. Esto es intencionado: significa que una instancia configurada a medias nunca envía correo en silencio desde una dirección equivocada, y puedes leer exactamente qué se habría enviado mientras haces pruebas.

:::note
Si alguien dice "nunca me llegó la invitación" en una instancia nueva, casi siempre es por este valor por defecto. El correo existe, en el archivo de log. Consulta @doc(troubleshooting.emailDelivery).
:::

Tienes dos formas compatibles de enviar correo real: cualquier servidor SMTP, o el servicio Resend.

## Opción 1: SMTP

::::steps
:::step title="Define el mailer y los datos del servidor"
En `.env`, define:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

Funciona con cualquier proveedor de correo transaccional o servidor de correo propio que tenga credenciales SMTP.
:::

:::step title="Define la identidad del remitente"
Define la dirección y el nombre que verán tus usuarios:

```bash
MAIL_FROM_ADDRESS=kollek@yourdomain.com
MAIL_FROM_NAME="KolleK"
```

Usa un dominio que controles y que tengas configurado para el envío (SPF y DKIM en tu proveedor), o tu correo acabará en la carpeta de spam.
:::

:::step title="Aplica los cambios y haz una prueba"
Recrea los contenedores y a continuación provoca un correo real, por ejemplo solicitando un enlace mágico desde la página de inicio de sesión:

```bash
docker compose up -d
```
:::
::::

## Opción 2: Resend

Si usas [Resend](https://resend.com), define:

```bash
USE_RESEND=true
RESEND_API_KEY=re_your_api_key
```

Los correos se envían entonces a través de la API de Resend en lugar de SMTP, y cada envío registra junto a él el identificador del mensaje en Resend.

## Comprobar que la entrega funciona

KolleK registra cada correo que envía, por usuario, con su asunto, cuerpo y estado de entrega. Después de tu prueba, revisa dos lugares:

- Tu bandeja de entrada, por el motivo obvio.
- La página de **correos enviados** del destinatario en su perfil, que lista lo que la instancia le envió. Consulta @doc(activity.logAndSentEmails, "Tu registro de actividad personal y tus correos enviados").

Señales habituales de fallo:

- **No llega nada y no hay ningún error.** El mailer sigue siendo `log`. Comprueba que `.env` se aplicó recreando los contenedores.
- **Los correos se envían pero acaban en spam.** El dominio del remitente no está autenticado. Configura SPF y DKIM en tu proveedor.
- **Errores de envío en el log.** Las credenciales o los datos del servidor son incorrectos. Los logs del worker de la cola contienen el mensaje de error del proveedor.

Los correos los envía la cola en segundo plano, así que el contenedor **queue** debe estar en funcionamiento para que algo salga de la instancia.

## Por dónde seguir

- Reconoce los correos que envía tu instancia en @doc(reference.emailsSent).
- Diagnostica problemas de entrega en @doc(troubleshooting.emailDelivery).
