---
id: tutorials.selfHostWithDocker
title: "Tutorial: Aloja tu propia instancia de KolleK con Docker"
slug: aloja-tu-propia-instancia-de-kollek-con-docker
section: tutoriales
---

# Tutorial: Aloja tu propia instancia de KolleK con Docker

En este tutorial llevarás una máquina sin nada instalado hasta una instancia de KolleK en funcionamiento: clonarás el proyecto, configurarás el entorno, generarás la clave de aplicación, arrancarás la pila, crearás la primera cuenta, y concederás el primer acceso de administrador de instancia. Al final tendrás una instancia funcionando y sabrás dónde continúan las guías operativas más avanzadas.

Seguiremos a Alex, que está configurando una instancia para su club de coleccionistas en un pequeño servidor doméstico. Los pasos son idénticos en un VPS o en un portátil.

Cuenta con que esto te llevará entre quince y treinta minutos, la mayor parte esperando la primera construcción.

## Antes de empezar

Necesitas:

- Una máquina con **Docker Engine 24 o superior** y el **plugin de Compose** (el comando `docker compose`, no el antiguo `docker-compose`).
- **Git**, para clonar el proyecto.
- Una terminal y cierta soltura básica ejecutando comandos en ella.

También ayuda repasar antes la @doc(selfHosting.index, "descripción general del alojamiento propio"), porque presenta la única regla en la que insistirá este tutorial: la clave de aplicación se define una vez y nunca se cambia.

## Paso 1: Clona el proyecto y crea tu configuración

```bash
git clone https://github.com/djaiss/beaver.git
cd beaver
cp .env.docker.example .env
```

El archivo `.env` es la configuración de tu instancia. Todo lo que un operador toca habitualmente vive ahí, y la @doc(selfHosting.configure, "guía de configuración") lo recorre grupo por grupo. Para un primer arranque, solo los dos pasos siguientes son obligatorios.

## Paso 2: Genera la clave de aplicación

KolleK cifra los datos sensibles en reposo con una clave que generas una sola vez:

```bash
docker compose run --rm app php artisan key:generate --show
```

Copia el resultado (empieza por `base64:`) y pégalo en `.env` como valor de `APP_KEY`.

:::warning
Define la clave de aplicación una sola vez y nunca la cambies en una instancia ya en funcionamiento. Todo lo cifrado, lo que incluye nombres, elementos y sesiones, queda ilegible de forma permanente con una clave distinta. Guarda una copia de la clave en un lugar seguro, porque una copia de seguridad de la base de datos solo se puede restaurar con la clave que la cifró.
:::

La historia completa, incluyendo cómo se admite una rotación deliberada de la clave, está en @doc(selfHosting.applicationKeyAndEncryption).

## Paso 3: Revisa las contraseñas y la URL

Abre `.env` en un editor y comprueba tres cosas:

- **`DB_PASSWORD` y `DB_ROOT_PASSWORD`.** Ambas vienen con valores de ejemplo. Cámbialas por contraseñas propias y seguras antes del primer arranque, porque es en ese primer arranque cuando se crea la base de datos con ellas.
- **`APP_URL`.** La dirección que escribirán tus usuarios. Alex define `http://server.local:8000` para la red del club. El valor por defecto es `http://localhost:8000`.
- **`APP_PORT`.** El puerto publicado, `8000` a menos que lo cambies.

## Paso 4: Arranca la pila

```bash
docker compose up -d --build
```

La primera ejecución construye la imagen y tarda unos minutos. Compose arranca entonces cuatro contenedores:

- **app**, el servidor web. Es el único rol que ejecuta migraciones de base de datos, así que el esquema se configura exactamente una vez.
- **queue**, el worker en segundo plano que envía correos y procesa trabajos.
- **scheduler**, que ejecuta las tareas de mantenimiento diarias.
- **mysql**, la base de datos.

Comprueba que todo está activo con `docker compose ps`. Cuando el contenedor app informe que está saludable, abre tu `APP_URL` en un navegador. Deberías ver la pantalla de inicio de sesión de KolleK.

## Paso 5: Crea la primera cuenta

Ve a la página de registro y date de alta. Esto funciona exactamente igual que para cualquier usuario, el recorrido está en @doc(accounts.create), y te convierte en el propietario de la primera cuenta de la instancia.

Alex se registra, aterriza en la lista de primeros pasos, y se resiste a catalogar nada hasta que termina el trabajo de operador.

## Paso 6: Concede el primer administrador de instancia

Un administrador de instancia puede ver todas las cuentas de la instancia, desde el panel de administración de instancia. El indicador se concede desde la línea de comandos:

```bash
docker compose exec app php artisan kollek:make-instance-administrator you@example.com
```

Usa el correo con el que te acabas de registrar. El mismo comando con `--revoke` retira el indicador. Qué hace, y qué deliberadamente no hace, el indicador se explica en @doc(instanceAdmin.grantAccess).

## El resultado

Tienes una instancia funcionando: la aplicación web respondiendo en tu URL, un worker de cola y un programador ejecutándose junto a ella, datos en un volumen de base de datos con nombre, y tú como propietario de la cuenta y administrador de la instancia a la vez. Los miembros del club ya pueden registrar sus propias cuentas, o puedes @doc(tutorials.inviteHousehold, "invitar a gente a la tuya").

## Una cosa que hacer antes de relajarte

Recién instalada, la instancia solo escribe el correo saliente en un archivo de log en lugar de enviarlo. Las invitaciones, los enlaces mágicos y los restablecimientos de contraseña no llegarán a ningún sitio en silencio hasta que configures un servidor de correo real. Eso es intencionado, y arreglarlo es una tarea corta: @doc(selfHosting.setupEmailDelivery).

## Errores habituales que evitar

- **Perder la clave de aplicación.** Haz una copia de seguridad ahora, por separado de la base de datos. Sin ella, las copias de seguridad son texto cifrado.
- **Dejar las contraseñas de ejemplo de la base de datos.** Cámbialas antes del primer arranque, no después.
- **Saltarte la configuración del correo.** El primer aviso de "mi invitación nunca llegó" será por esto.

## Por dónde seguir

- Repasa todos los ajustes que te saltaste en @doc(selfHosting.configure).
- Configura las @doc(selfHosting.backupAndRestore, "copias de seguridad") antes de que el catálogo crezca y se vuelva valioso.
- Cuando salga una versión nueva, sigue @doc(selfHosting.upgrade).
