---
id: troubleshooting.featureStatus
title: Estado de las funcionalidades y hoja de ruta
slug: estado-de-las-funcionalidades
section: solucion-de-problemas
---

# Estado de las funcionalidades y hoja de ruta

KolleK está en crecimiento, y algunas funcionalidades son visibles antes de estar terminadas. Esta página es la única lista honesta de lo que está totalmente disponible hoy y de lo que todavía está en camino, así ninguna otra página tiene que andarse con rodeos. Cuando el producto avanza, esta página avanza con él.

## Disponible ahora

Todo lo demás documentado en este portal funciona tal como se describe, incluyendo:

- Colecciones, elementos, ejemplares, fotos, etiquetas, categorías, sets y series.
- Tipos de colección con campos personalizados, incluida la importación y exportación de definiciones de tipo como JSON.
- El historial completo del ejemplar: transacciones, valoraciones, seguros, préstamos, mantenimiento, procedencia, historial de ubicaciones y documentos, con la línea de tiempo unificada.
- Colaboración con los roles de propietario, editor y lector, e invitaciones por correo electrónico.
- Autenticación de dos factores, enlaces mágicos, claves de API y correos de alerta de seguridad.
- La API JSON completa con su referencia generada en `/docs/api`.
- Autoalojamiento con Docker, datos cifrados en reposo, papelera con restauración, y estadísticas por colección.

## Todavía no

### Búsqueda global

El cuadro de búsqueda del panel principal es un marcador de posición y todavía no busca nada. Lo que funciona hoy: filtrar los elementos de una colección que tienes abierta (consulta @doc(collections.chooseView)), y buscar en la @doc(photos.library, "biblioteca de fotos").

### Visibilidad y compartición de colecciones

Cada colección lleva una configuración de visibilidad (privada, compartida o pública), y esa configuración se guarda, pero todavía no se aplica. Todos los miembros de una cuenta pueden seguir explorando todas las colecciones que hay en ella, y no existe ningún enlace público, así que una colección marcada como pública no es accesible desde fuera de la cuenta en absoluto. Define la visibilidad ahora para registrar tu intención; entrará en vigor cuando llegue la compartición. Consulta @doc(sharing.overview).

### Entrega de webhooks

Puedes registrar destinos de webhook, y cada uno recibe una clave secreta de firma, pero todavía ningún evento de la aplicación dispara un webhook. La maquinaria de firma y entrega está lista, a la espera de que se conecten los eventos. Configúralo ya si quieres; las entregas llegarán a medida que crezca el dominio. Consulta @doc(webhooks.overview).

### Importación y exportación de elementos y colecciones

La importación y exportación existen solo para las definiciones de tipo de colección. Todavía no hay importación ni exportación a nivel de elemento ni de colección completa. Para sacarlo todo, quienes autoalojan la instancia cuentan con copias de seguridad completas de la instancia; consulta @doc(dataSafety.backupCollectionData).

### Administración de la instancia: soporte y reseñas

En el panel de administración de la instancia, las áreas de Soporte y Reseñas son marcadores de posición que lo indican como tal. El resto del panel funciona; consulta @doc(instanceAdmin.panel).

## Cómo leer esta página

Nada aquí es una promesa con fecha. "Todavía no" significa que puede que ya exista el trabajo de base, pero no deberías planificar contando con la funcionalidad hasta que pase a la lista de arriba. Ante la duda, confía en esta página por encima de cualquier cosa que parezca sugerir lo contrario.

Las preguntas que esta página no responde probablemente estén en las @doc(troubleshooting.faq, "preguntas frecuentes").
