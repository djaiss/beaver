---
id: selfHosting.installDocker
title: Instalar con Docker
slug: instalar-con-docker
section: alojamiento-propio
---

# Instalar con Docker

Esta es la guía de instalación oficial. Te lleva desde una máquina con Docker hasta una instancia de KolleK en funcionamiento con tu primera cuenta creada. Cuenta con que el proceso completo te llevará unos quince minutos.

El archivo `docker/README.md` del repositorio documenta el mismo procedimiento desde el punto de vista del operador y se mantiene sincronizado con el código. Si esta página y ese archivo alguna vez no coinciden, confía en `docker/README.md`.

## Antes de empezar

Necesitas:

- Una máquina con **Docker Engine 24 o superior** y el **plugin de Compose** (`docker compose`).
- Una copia del repositorio de KolleK, clonada o descargada.
- Diez minutos de atención para el archivo de entorno. Ahí es donde ocurren los errores que importan.

Nada más. La pila trae su propia base de datos MySQL, y las sesiones, la caché y la cola dependen de la base de datos, así que no hay que instalar Redis.

## Instalación

::::steps
:::step title="Crea tu archivo de entorno"
Desde la raíz del repositorio, copia la plantilla de entorno de Docker:

```bash
cp .env.docker.example .env
```

Este archivo controla toda la pila. Lo editarás en los dos pasos siguientes.
:::

:::step title="Genera la clave de aplicación"
Genera una clave y copia el resultado:

```bash
docker compose run --rm app php artisan key:generate --show
```

Pega el valor impreso en `.env` como `APP_KEY`. Esta clave cifra tus datos en reposo. **Configúrala ahora y no la cambies nunca después.** Una clave modificada deja todos los campos cifrados y todas las sesiones ilegibles de forma permanente. Lee @doc(selfHosting.applicationKeyAndEncryption) antes de continuar si todavía no lo has hecho.
:::

:::step title="Revisa las contraseñas y la URL"
En `.env`, cambia `DB_PASSWORD` y `DB_ROOT_PASSWORD` respecto a sus valores de ejemplo, y define `APP_URL` con la dirección que visitarán tus usuarios. El valor por defecto es `http://localhost:8000`, que sirve perfectamente para una primera prueba en tu propia máquina.
:::

:::step title="Arranca la pila"
Construye e inicia todo:

```bash
docker compose up -d --build
```

La primera construcción tarda unos minutos. Cuando termina, el contenedor web aplica las migraciones de la base de datos automáticamente y la instancia queda disponible en tu `APP_URL`.
:::

:::step title="Crea tu primera cuenta"
Abre la URL en un navegador y usa la página de registro para darte de alta. Esto crea tu usuario personal y tu primera cuenta, exactamente como se describe en @doc(accounts.create).

::screenshot{label="Página de registro de una instancia recién instalada"}
:::

:::step title="Concédete acceso de administrador de instancia"
Si quieres el panel de administración de toda la instancia, concede el indicador a tu usuario:

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

Consulta @doc(instanceAdmin.grantAccess) para saber qué te da esto y qué no.
:::
::::

## Qué se está ejecutando realmente

La pila de Compose arranca cuatro contenedores. Tres de ellos ejecutan la misma imagen de KolleK con roles distintos, elegidos mediante la variable de entorno `CONTAINER_ROLE`:

- **app** sirve la aplicación web a través de nginx y PHP. Es el único contenedor que ejecuta migraciones de la base de datos, y lo hace al arrancar.
- **queue** procesa trabajos en segundo plano (correos, entregas, registro de actividad) de las colas `high`, `default` y `low`.
- **scheduler** dispara las tareas de mantenimiento diarias descritas en @doc(selfHosting.scheduledJobs).

El cuarto contenedor es **mysql**, con MySQL 8.4.

Tus datos viven en dos volúmenes de Docker con nombre, independientes de los contenedores: `db-data` para la base de datos y `storage-data` para las fotos y documentos subidos. Los contenedores se pueden reconstruir y reemplazar libremente; los volúmenes persisten.

:::note
Los tres contenedores de la aplicación deben compartir el mismo `.env`, y sobre todo la misma `APP_KEY`. El archivo de Compose ya lo organiza así. Mantén esa propiedad si personalizas la configuración.
:::

## Si prefieres ejecutar las migraciones tú mismo

Por defecto, el contenedor web migra la base de datos cada vez que arranca, lo que hace que las actualizaciones no requieran intervención. Si quieres control manual, define `RUN_MIGRATIONS=false` en `.env` y ejecuta las migraciones tú mismo cuando lo necesites:

```bash
docker compose exec app php artisan migrate --force
```

## Por dónde seguir

- Repasa @doc(selfHosting.configure) para entender qué más controla `.env`.
- Haz que el correo funcione en @doc(selfHosting.setupEmailDelivery). Hasta que lo hagas, las invitaciones y los enlaces de acceso van a un archivo de log en lugar de a una bandeja de entrada.
- Configura las @doc(selfHosting.backupAndRestore, "copias de seguridad") antes de meter datos reales.
