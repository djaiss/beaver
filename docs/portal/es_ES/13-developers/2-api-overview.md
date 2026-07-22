---
id: api.overview
title: Resumen de la API
slug: resumen-de-la-api
section: desarrolladores
---

# Resumen de la API

La API de KolleK es una API JSON que refleja la aplicación web punto por punto. Cada capacidad de la aplicación (crear colecciones, añadir elementos y ejemplares, registrar transacciones, gestionar miembros) tiene un endpoint equivalente, sujeto exactamente a las mismas reglas. Si tu rol te permite hacer algo en el navegador, tu token te permite hacerlo por HTTP. Si no te lo permite, la API lo rechaza de la misma forma en que lo haría la aplicación.

Esta página te da el modelo mental. La referencia completa y siempre actualizada de endpoints se genera a partir del código y la sirve tu instancia:

- `/docs/api` para la referencia navegable.
- `/docs/api.md` para toda la referencia como Markdown.
- `/docs/api/{section}.md` para una sola sección como Markdown, útil para pasar un solo tema a una herramienta.

:::note
En una instancia autoalojada, la referencia forma parte del sitio de marketing público, que está desactivado por defecto. Un operador la activa con el ajuste `SHOW_MARKETING_SITE`. Ver @doc(selfHosting.configure).
:::

## Limitada a tu cuenta

La API está limitada por inquilino. Un token pertenece a un usuario, y un usuario pertenece exactamente a una **@doc(accounts.usersAndRoles, "cuenta")**, así que cada solicitud se resuelve a través de esa cuenta. No puedes acceder a los datos de otra cuenta, y no pasas ningún identificador de cuenta en ningún sitio. No hay nada que configurar: te autenticas, y ya estás dentro de tu propio espacio de trabajo.

Se aplican los mismos **@doc(accounts.usersAndRoles, "roles")** que en la aplicación. El token de un lector puede leer pero no escribir. El token de un editor puede gestionar el contenido del catálogo. Las acciones exclusivas de propietario (miembros, configuración de la cuenta) necesitan el token de un propietario.

## Cómo están estructurados los recursos

Los recursos se anidan de la misma forma en que **@doc(kollek.howOrganized, "está organizado KolleK")**:

- Tu **cuenta** contiene recursos a nivel de cuenta: miembros, tipos de colección, campos personalizados, etiquetas, ubicaciones, estados.
- Las **colecciones** contienen **elementos**, junto con categorías y sets.
- Los **elementos** contienen **fotos** y **ejemplares**.
- Los **ejemplares** llevan los recursos de historial: transacciones, valoraciones, registros de seguro, préstamos, registros de mantenimiento, eventos de procedencia, historial de ubicación, documentos, y la cronología combinada.

Las respuestas siguen aproximadamente la forma de JSON:API: cada recurso vuelve como `type`, `id`, `attributes` y `links`. Las listas se paginan con un sobre estándar, tratado en @doc(api.rateLimitsAndConventions).

## Qué cubre esta sección

Estas páginas cubren los primeros pasos y los conceptos que la referencia generada no puede enseñar: autenticación, convenciones y el estado actual de los webhooks. Para cualquier endpoint concreto, sus parámetros, y ejemplos de solicitud y respuesta resueltos, ve directamente a `/docs/api`.

:::note
No existe un modo de pruebas. Cada solicitud a la API se ejecuta contra tu cuenta real, así que ten cuidado con las llamadas destructivas mientras experimentas.
:::

## A dónde ir ahora

- Haz tu primera solicitud en @doc(api.authenticate).
- Repasa @doc(api.rateLimitsAndConventions) antes de escribir un cliente.
- Explora la referencia generada en `/docs/api` de tu instancia.
