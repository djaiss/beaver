---
id: selfHosting.configure
title: Configura tu instancia
slug: configura-tu-instancia
section: alojamiento-propio
---

# Configura tu instancia

Todo lo relativo a tu instancia se configura a través del archivo `.env` que creaste durante la @doc(selfHosting.installDocker, "instalación"). Esta página recorre los ajustes que un operador realmente utiliza, agrupados por lo que hacen, en lugar de enumerar todas las variables que contiene la plantilla.

Después de modificar `.env`, aplica los cambios recreando los contenedores:

```bash
docker compose up -d
```

## Identidad y URL

- `APP_NAME` es el nombre que se muestra en la interfaz y en los correos. Por defecto es `Kollek`.
- `APP_URL` es la dirección pública de tu instancia. Los enlaces de los correos se construyen a partir de ella, así que debe ser la dirección que realmente usan tus usuarios.
- `APP_PORT` es el puerto del host que publica el contenedor web, `8000` por defecto.

## La clave de aplicación

`APP_KEY` cifra los datos sensibles en reposo. La defines una vez durante la instalación y no la cambias a la ligera. Es lo bastante importante como para tener @doc(selfHosting.applicationKeyAndEncryption, "su propia página"), que también cubre el mecanismo de rotación `APP_PREVIOUS_KEYS`.

## Base de datos

`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` y `DB_ROOT_PASSWORD` configuran el contenedor de MySQL incluido. Cambia ambas contraseñas respecto a sus valores de ejemplo antes del primer arranque. `RUN_MIGRATIONS` controla si el contenedor web migra al arrancar (`true` por defecto).

## Correo

`MAIL_MAILER` decide cómo sale el correo de tu instancia, y por defecto es `log`.

:::note
Con el mailer `log` por defecto, no se envía ningún correo. Las invitaciones, los enlaces mágicos, los restablecimientos de contraseña y las alertas de seguridad se escriben en el log de la aplicación en su lugar. Configurar un mailer real es la única pieza de configuración que casi toda instancia necesita. Consulta @doc(selfHosting.setupEmailDelivery).
:::

## Almacenamiento de archivos

`FILESYSTEM_DISK` es `local` por defecto: las fotos y documentos subidos se guardan en el volumen `storage-data`. Para usar almacenamiento de objetos compatible con S3, ponlo en `s3` y completa las variables `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` y, para proveedores que no sean AWS, `AWS_ENDPOINT`. En cualquier caso, los archivos se sirven a los usuarios a través de rutas privadas verificadas por cuenta, nunca como URLs públicas.

## Mantenimiento

- `TRASH_RETENTION_DAYS` es el tiempo que los objetos eliminados de forma reversible permanecen en la @doc(dataSafety.restoreFromTrash, "papelera") antes de que la limpieza nocturna los elimine para siempre. El valor por defecto es 30 días.
- `ACCOUNT_DELETION_NOTIFICATION_EMAIL` es la dirección a la que se notifica cuando un usuario elimina su propio usuario o es eliminado por la @doc(users.inactiveDeletion, "limpieza por inactividad"). Apúntala a ti mismo para que ninguna baja pase desapercibida.

## El sitio público de marketing

`SHOW_MARKETING_SITE` es `false` por defecto, lo que significa que tu instancia solo sirve la aplicación en sí. Ponlo en `true` para servir también las páginas públicas de marketing y la referencia de API generada en `/docs/api`. La mayoría de las instancias privadas lo dejan desactivado; actívalo si tus desarrolladores quieren tener la referencia de la API disponible localmente.

## Lo que no necesitas configurar

Las sesiones (`SESSION_DRIVER`), la caché (`CACHE_STORE`) y la cola (`QUEUE_CONNECTION`) dependen todas de la base de datos (`database`) de fábrica. Los valores por defecto son correctos para la pila proporcionada, y no hay Redis ni ningún otro servicio que añadir. Déjalos como están a menos que sepas con precisión por qué los cambias.

## Por dónde seguir

- Haz que el correo real funcione en @doc(selfHosting.setupEmailDelivery).
- Entiende la clave que debes proteger en @doc(selfHosting.applicationKeyAndEncryption).
- Configura las @doc(selfHosting.backupAndRestore, "copias de seguridad").
