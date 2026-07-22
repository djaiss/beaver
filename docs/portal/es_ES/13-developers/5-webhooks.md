---
id: webhooks.overview
title: Webhooks
slug: webhooks
section: desarrolladores
---

# Webhooks

Los webhooks permiten que un sistema externo reciba una llamada HTTP de KolleK cuando ocurre algo en tu cuenta. Puedes configurarlos hoy, y esta página muestra cómo. Pero lee el siguiente párrafo primero, porque enmarca todo lo demás.

:::note
Actualmente ningún evento de la aplicación activa un webhook. El mecanismo de registro, firma y entrega ya está implementado y probado, pero los eventos solo empezarán a dispararse a medida que crezca el dominio de colecciones. Configura tu receptor ahora si quieres; simplemente no dependas de él para nada todavía. La @doc(troubleshooting.featureStatus, "página de estado de funciones") lleva el seguimiento de cuándo cambia esto.
:::

## Lo que existe hoy

Registrar un endpoint guarda una URL de destino con su propio secreto de firma. Cuando KolleK finalmente dispare eventos, cada uno se entregará a todos los endpoints activos que hayas registrado, firmado para que tu receptor pueda verificar que realmente vino de tu instancia.

Los endpoints de webhook pertenecen a tu usuario, no a toda la cuenta.

## Registra un endpoint

Desde la aplicación, abre la configuración de tu perfil y ve a **Webhooks**. Añade la URL en la que escucha tu receptor, con una etiqueta para que recuerdes para qué es. Cada endpoint recibe su propio secreto de firma, una cadena de 64 caracteres generada al crear el endpoint. Guárdalo junto a tu receptor.

Un operador también puede crear un endpoint desde la línea de comandos:

```bash
php artisan kollek:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

El comando imprime el id del endpoint y su secreto de firma.

## La carga útil que debe esperar tu receptor

Cada entrega es un `POST` en JSON con esta forma:

```json
{
  "event": "example.event",
  "happened_at": "2026-07-20T14:30:00+00:00",
  "data": {}
}
```

- `event` nombra lo que ocurrió. Todavía no hay ningún nombre de evento definido.
- `happened_at` es una marca de tiempo ISO 8601 de cuándo ocurrió.
- `data` lleva la carga útil de ese evento.

## Verificar firmas

Cada entrega incluye un encabezado `Signature`: un hash HMAC SHA256 del cuerpo bruto de la solicitud, calculado con el secreto de firma de tu endpoint. Vuelve a calcular el mismo hash en tu lado y compáralo. Si difieren, descarta la solicitud, porque no vino de tu instancia.

```php
$computed = hash_hmac('sha256', $rawRequestBody, $signingSecret);

if (! hash_equals($computed, $request->header('Signature'))) {
    abort(401);
}
```

## Entrega y reintentos

Las entregas se encolan y se envían en segundo plano. Una entrega fallida se reintenta hasta 3 veces con retroceso exponencial. Tu receptor debería responder rápido con un estado 2xx y realizar su trabajo real de forma asíncrona.

En una instancia autoalojada, las entregas se ejecutan en el trabajador de la cola, así que el rol de cola debe estar en funcionamiento. Ver @doc(selfHosting.installDocker).

## A dónde ir ahora

- Comprueba qué está activo y qué está pendiente en la @doc(troubleshooting.featureStatus, "página de estado de funciones").
- Mientras tanto, desarrolla contra la API, empezando por @doc(api.authenticate).
