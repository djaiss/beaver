---
id: selfHosting.backupAndRestore
title: Haz copias de seguridad y restaura tu instancia
slug: haz-copias-de-seguridad-y-restaura-tu-instancia
section: alojamiento-propio
---

# Haz copias de seguridad y restaura tu instancia

No hay ninguna copia de seguridad automática dentro de KolleK. Proteger los datos es tarea del operador, y esta página es el procedimiento. También es, hoy por hoy, la respuesta real a "cómo exporto todo", tal como explica @doc(dataSafety.backupCollectionData) desde el punto de vista del coleccionista.

## Qué es una copia de seguridad completa

Tres cosas, y las tres importan:

1. **La base de datos**, en el volumen `db-data`. Todos los registros: cuentas, colecciones, elementos, ejemplares, historial.
2. **El volumen de almacenamiento**, `storage-data`. Todas las fotos y documentos subidos.
3. **La clave de aplicación**, `APP_KEY` de tu `.env` (más `APP_PREVIOUS_KEYS` si la has definido).

:::warning
Una copia de seguridad sin su clave de aplicación correspondiente no es una copia de seguridad. Los campos cifrados se restauran como texto cifrado ilegible sin la clave que los escribió. Guarda la clave junto a, o al lado de, cada copia de seguridad que hagas. Consulta @doc(selfHosting.applicationKeyAndEncryption).
:::

## Copia de seguridad

Vuelca la base de datos:

```bash
docker compose exec mysql mysqldump -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" > kollek-backup.sql
```

Archiva el volumen de almacenamiento:

```bash
docker run --rm -v kollek_storage-data:/data -v "$PWD":/backup alpine tar czf /backup/kollek-storage.tar.gz -C /data .
```

Copia ambos archivos, y una copia de tu `.env`, fuera del servidor. Automatiza esto con una tarea cron nocturna y conserva más de una generación; una copia de seguridad que nunca has restaurado es una esperanza, no un plan.

## Restauración

En una máquina nueva, restaura en este orden:

1. Instala la misma versión de KolleK siguiendo @doc(selfHosting.installDocker), pero define `APP_KEY` (y `APP_PREVIOUS_KEYS`) a partir de tu copia de seguridad en lugar de generar una clave nueva.
2. Arranca la pila una vez para que existan los volúmenes, y luego carga el volcado de la base de datos:

```bash
docker compose exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" < kollek-backup.sql
```

3. Descomprime el archivo de almacenamiento en el volumen de almacenamiento:

```bash
docker run --rm -v kollek_storage-data:/data -v "$PWD":/backup alpine tar xzf /backup/kollek-storage.tar.gz -C /data
```

4. Reinicia la pila con `docker compose up -d` e inicia sesión para verificar.

## El comando que lo elimina todo

:::warning
`docker compose down -v` elimina los volúmenes con nombre, es decir, la base de datos y todos los archivos subidos. No uses nunca el indicador `-v` en una instancia real. `docker compose down` a secas es seguro y deja los volúmenes intactos.
:::

## Por dónde seguir

- Entiende qué protege la clave en @doc(selfHosting.applicationKeyAndEncryption).
- Consulta qué pueden exportar los coleccionistas desde dentro de la aplicación en @doc(dataSafety.backupCollectionData).
