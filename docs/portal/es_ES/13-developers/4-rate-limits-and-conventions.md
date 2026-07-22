---
id: api.rateLimitsAndConventions
title: Límites de frecuencia y convenciones
slug: limites-de-frecuencia-y-convenciones
section: desarrolladores
---

# Límites de frecuencia y convenciones

Un puñado de convenciones se aplican a toda la API. Aprenderlas una vez te ahorra sorpresas en cada endpoint, así que viven aquí en lugar de repetirse a lo largo de la referencia.

## Límites de frecuencia

- Las solicitudes autenticadas están limitadas a **60 por minuto** por usuario.
- `POST /api/register` y `POST /api/login` están limitadas a **6 por minuto**, lo que protege contra el relleno de credenciales.

Cuando superas un límite, la API responde con HTTP 429. Reduce el ritmo y reintenta después de un momento. Si estás escribiendo una importación masiva, distribuye tus solicitudes en lugar de dispararlas lo más rápido posible, y recuerda que la API trabaja con un objeto por solicitud, ya que no hay endpoints masivos.

## Paginación

Los endpoints de listado están paginados y comparten un mismo sobre:

- `data` contiene la página de recursos.
- `links` contiene las URL `first`, `last`, `prev` y `next`.
- `meta` contiene la página actual, el conteo total y otros detalles relacionados.

Las páginas contienen **10 recursos por defecto**. Pide más con el parámetro de consulta `per_page`, hasta un **máximo de 100**. Sigue `links.next` hasta que sea `null` para recorrer una lista completa.

## El dinero está en la unidad más pequeña de la moneda

Cada cantidad en la API (valores estimados, importes de transacciones, depósitos, valores asegurados) es un número entero en la unidad más pequeña de su moneda. Para dólares y euros eso significa céntimos: una compra de 49,99 $ viaja como `4999`. Esto evita por completo los redondeos de coma flotante. Convierte para mostrar en tu propio código, y recuerda que cada @doc(collections.overview, "colección") tiene su propia moneda.

## Lo prohibido se muestra como no encontrado

La API aplica los mismos @doc(accounts.usersAndRoles, "roles") que la aplicación web, con un matiz deliberado: una acción que no tienes permitido realizar, o un recurso de otra cuenta, responde **404 Not Found**, no 403 Forbidden. Quien llama no puede distinguir "esto no existe" de "esto no es tuyo", así que la API nunca confirma qué existe fuera de tu cuenta.

:::note
Si un endpoint devuelve inesperadamente 404 en un objeto que puedes ver en la aplicación, comprueba el rol del usuario cuyo token estás usando. El token de un lector recibe 404 en cada escritura.
:::

## Errores y validación

Una validación fallida responde HTTP 422 con un `message` y un objeto `errors` indexado por nombre de campo. El resto de errores siguen la semántica HTTP habitual: 401 cuando falta el token o está revocado, 404 como se describe arriba, 429 para los límites de frecuencia.

## A dónde ir ahora

- Ve estas convenciones aplicadas a endpoints reales en la referencia generada en `/docs/api`.
- ¿Listo para la entrega de eventos algún día? Lee en qué punto está hoy @doc(webhooks.overview).
