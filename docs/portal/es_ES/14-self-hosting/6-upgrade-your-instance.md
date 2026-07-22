---
id: selfHosting.upgrade
title: Actualiza tu instancia
slug: actualiza-tu-instancia
section: alojamiento-propio
---

# Actualiza tu instancia

Actualizar KolleK está diseñado para ser aburrido: descarga la versión nueva, reconstruye, listo. Esta página explica por qué eso es seguro, y el único paso posterior a la actualización que conviene conocer.

## Por qué las actualizaciones no pierden datos

Dos propiedades hacen segura la ruta de actualización:

- **Tus datos viven en volúmenes con nombre** (`db-data` para la base de datos, `storage-data` para los archivos), independientes de los contenedores y de la imagen. Reconstruir los contenedores no los toca.
- **Las migraciones solo avanzan hacia adelante.** El contenedor web aplica las migraciones de base de datos pendientes al arrancar con `migrate --force`, y KolleK nunca distribuye una migración que reinicie o reescriba datos de forma destructiva. Una actualización solo añade cosas a tu esquema.

## Actualización

::::steps
:::step title="Haz una copia de seguridad primero"
Realiza un volcado de la base de datos y un archivo del almacenamiento como se describe en @doc(selfHosting.backupAndRestore). Las actualizaciones son seguras por diseño, pero una copia de seguridad convierte "seguro por diseño" en "seguro, punto".
:::

:::step title="Obtén la nueva versión"
Desde el directorio del repositorio, descarga la versión a la que vas a actualizar:

```bash
git pull
```
:::

:::step title="Reconstruye y reinicia"
```bash
docker compose up -d --build
```

Compose reconstruye la imagen y recrea los contenedores. Al arrancar, el contenedor web aplica automáticamente cualquier migración nueva, y la instancia vuelve a estar disponible en tu `APP_URL`.
:::
::::

Si prefieres controlar las migraciones manualmente, define `RUN_MIGRATIONS=false` y ejecuta tú mismo `docker compose exec app php artisan migrate --force` como parte del procedimiento, tal como se describe en @doc(selfHosting.installDocker).

## El paso del índice de búsqueda de fotos

Una actualización incluye una tarea de mantenimiento puntual: las instancias anteriores a la pantalla de biblioteca de fotos necesitan que se construya una vez su índice de búsqueda de fotos, o la búsqueda de fotos se queda vacía para las fotos existentes.

```bash
docker compose exec app php artisan photos:rebuild-search-index
```

El comando es idempotente y se puede ejecutar sin riesgo en cualquier instancia, así que, en caso de duda, ejecútalo. También rellena las dimensiones de imagen de las fotos subidas antes de que se empezaran a registrar.

:::note
No cambies `APP_KEY` como parte de una actualización. La clave sobrevive a todas las versiones. Si una guía de actualización alguna vez parece pedirte una clave nueva, la estás leyendo mal. Consulta @doc(selfHosting.applicationKeyAndEncryption).
:::

## Por dónde seguir

- Mantén las @doc(selfHosting.backupAndRestore, "copias de seguridad") al día para que cada actualización parta de una.
- Revisa @doc(selfHosting.scheduledJobs), que se reanudan automáticamente en cuanto vuelve a estar activo el contenedor del programador.
