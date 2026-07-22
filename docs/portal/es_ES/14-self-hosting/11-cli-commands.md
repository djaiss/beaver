---
id: selfHosting.cliCommands
title: Administra desde la línea de comandos
slug: administra-desde-la-linea-de-comandos
section: alojamiento-propio
---

# Administra desde la línea de comandos

Algunas tareas de operador viven en la línea de comandos y no en la aplicación web. Esta página enumera los comandos artisan que probablemente necesites al gestionar una instancia, con un enlace a la página completa de cada uno.

En una instalación con Docker, ejecuta cada comando a través del contenedor web:

```
docker compose exec app php artisan <command>
```

## Operación del día a día

### Conceder o revocar la administración de instancia

```
php artisan kollek:make-instance-administrator you@example.com
php artisan kollek:make-instance-administrator you@example.com --revoke
```

Concede (o retira) el indicador de administrador global al usuario con ese correo. Así es como se inicializa el primer administrador tras la instalación. Consulta @doc(instanceAdmin.grantAccess).

### Crear un endpoint de webhook

```
php artisan kollek:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

Registra un endpoint de webhook para un usuario e imprime su ID y su secreto de firma. Los usuarios también pueden hacer esto ellos mismos desde los ajustes de su perfil. Ten en cuenta que todavía ningún evento de la aplicación dispara webhooks; consulta @doc(webhooks.overview).

### Reconstruir el índice de búsqueda de fotos

```
php artisan photos:rebuild-search-index
```

Reconstruye el índice de búsqueda que hay detrás de la biblioteca de fotos y rellena las dimensiones de imagen que falten. Ejecútalo una vez después de actualizar a una versión que introduce la pantalla de fotos. Es seguro volver a ejecutarlo en cualquier momento; omite las fotos cuyos archivos falten y no cambia nada más. Consulta @doc(selfHosting.upgrade).

### Preparar un idioma para su traducción

```
php artisan kollek:localize fr_FR
```

Extrae cada cadena traducible de la aplicación y la sincroniza con el archivo JSON del idioma en `lang/`. Consulta @doc(selfHosting.addLanguage).

## Solo para desarrollo

Existen otros dos comandos en el código, y ninguno tiene cabida en una instancia de producción. `kollek:bruno` reinicia la base de datos con datos de ejemplo para probar clientes de la API, lo que destruiría datos reales, y `kollek:sync-skills` mantiene las herramientas propias del proyecto. Como operador, puedes ignorar ambos.

:::warning
No ejecutes nunca `kollek:bruno` en una instancia real. Borra la base de datos y la vuelve a poblar con datos de demostración.
:::

## Por dónde seguir

- Inicializa tu administrador en @doc(instanceAdmin.grantAccess).
- Mantén la instancia al día con @doc(selfHosting.upgrade).
- Traduce la interfaz en @doc(selfHosting.addLanguage).
