---
id: selfHosting.applicationKeyAndEncryption
title: La clave de aplicación y el cifrado
slug: la-clave-de-aplicacion-y-el-cifrado
section: alojamiento-propio
---

# La clave de aplicación y el cifrado

Esta página explica la regla operativa más importante para ejecutar KolleK. Todo lo demás sobre la instancia se puede recuperar con paciencia. Este es el único ajuste que puede destruir datos de forma irreversible.

## Qué hace la clave

KolleK cifra los campos sensibles en reposo con la clave de aplicación de la instancia, el valor `APP_KEY` de tu `.env`. Nombres, detalles de elementos, valores de campos personalizados, nombres de archivo, registros de correo, secretos de webhooks: alrededor de treinta modelos tienen columnas cifradas. Lo que llega a la base de datos en esos campos es texto cifrado, ilegible sin la clave. La misma clave también protege las sesiones de los usuarios.

Esto es lo que @doc(dataSafety.howProtected) describe desde el punto de vista del usuario. A nivel operativo significa que la clave no es un detalle de configuración más. Es la mitad de tus datos.

## La regla

:::warning
Define la clave de aplicación una sola vez, antes del primer arranque, y nunca la cambies en una instancia ya en funcionamiento. Si la clave se pierde o se cambia, todas las columnas cifradas y todas las sesiones quedan ilegibles de forma permanente. No hay recuperación posible, ni vía de soporte, ni herramienta que pueda devolver los datos.
:::

Tres consecuencias prácticas:

- **Respalda la clave junto con los datos.** Una copia de seguridad de la base de datos sin su clave correspondiente se restaura como texto cifrado. Guarda la clave en un gestor de contraseñas o en un almacén de secretos, por separado del servidor.
- **Mantenla idéntica en todas partes.** Los tres contenedores de la aplicación (web, cola, programador) deben ejecutarse con la misma clave. El archivo de Compose incluido comparte un único `.env`, lo que ya lo garantiza. Conserva esa propiedad en cualquier despliegue personalizado.
- **No la regeneres "por si acaso".** Ejecutar `key:generate` contra una instancia en funcionamiento es el desastre autoinfligido clásico. La instancia se niega a arrancar sin una clave precisamente para que nadie arranque una sin querer sin clave y genere una nueva a mitad de su vida.

## Rotar la clave de forma deliberada

Algunos operadores deben rotar sus claves periódicamente por motivos de política interna. KolleK lo permite mediante claves anteriores: la `APP_KEY` actual cifra todo lo nuevo, mientras que las claves listadas en `APP_PREVIOUS_KEYS` (separadas por comas) todavía pueden descifrar los datos existentes.

```bash
APP_KEY=base64:NEW_KEY_HERE
APP_PREVIOUS_KEYS=base64:OLD_KEY_HERE
```

Genera una clave nueva con `php artisan key:generate --show` (nunca con `key:generate` a secas, que sobrescribe tu clave activa), mueve la clave antigua a `APP_PREVIOUS_KEYS`, define la nueva como `APP_KEY` y recrea los contenedores.

:::warning
Nunca elimines una clave de `APP_PREVIOUS_KEYS` mientras exista algún dato cifrado con ella. Los datos solo se vuelven a cifrar con la clave nueva cuando se escriben de nuevo, así que registros antiguos pueden depender de la clave antigua indefinidamente.
:::

Si no te exigen rotar la clave, la política segura más simple es: una sola clave, definida una vez, bien respaldada.

## Por dónde seguir

- Asegúrate de que la clave forma parte de tu @doc(selfHosting.backupAndRestore, "plan de copias de seguridad y restauración").
- Lee la visión del cifrado orientada al usuario en @doc(dataSafety.howProtected).
