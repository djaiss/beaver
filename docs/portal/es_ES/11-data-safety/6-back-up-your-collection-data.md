---
id: dataSafety.backupCollectionData
title: Haz una copia de seguridad de los datos de tu colección
slug: haz-una-copia-de-seguridad-de-los-datos-de-tu-coleccion
section: seguridad-y-mantenimiento-de-datos
---

# Haz una copia de seguridad de los datos de tu colección

"¿Cómo saco mis datos de aquí" merece una respuesta directa. Esta página expone con claridad qué puede exportar KolleK hoy desde dentro de la aplicación, qué todavía no puede, y cuál es el camino real de copia de seguridad mientras tanto.

## Lo que puedes exportar hoy

**Definiciones de tipo de colección.** Un @doc(collectionTypes.overview, "tipo de colección") se puede exportar como un archivo JSON (su nombre, color, grupos de campos, campos y opciones) e importar en cualquier cuenta de KolleK. Ver @doc(collectionTypes.importExport).

Esa es la lista honesta y completa.

## Lo que todavía no puedes exportar

Actualmente no existe una exportación integrada de elementos, ejemplares, fotos o colecciones enteras, ni la importación correspondiente. Los datos de tu catálogo todavía no se pueden extraer de la aplicación como un archivo desde la interfaz.

:::note
La importación y exportación de elementos y colecciones está en la lista de funciones planeadas. La @doc(troubleshooting.featureStatus, "página de estado de funciones") es el registro actualizado de en qué punto está esto, así que consúltala en lugar de suponer.
:::

Si hoy necesitas acceso estructurado a tus datos, la @doc(api.overview, "API JSON") puede leer todo en tu cuenta, lo cual es un camino viable para quienes tienen conocimientos técnicos.

## El camino real de copia de seguridad hoy

Si tu instancia es autoalojada, la copia de seguridad fiable se hace a nivel de instancia: un volcado de la base de datos más un archivo del volumen de almacenamiento que contiene las fotos y documentos. Eso captura absolutamente todo, incluyendo lo que la exportación dentro de la aplicación no puede alcanzar. La guía paso a paso vive en @doc(selfHosting.backupAndRestore).

Si alguien más aloja KolleK por ti, esa capacidad de copia de seguridad la tiene esa persona. Pregúntale cuáles son sus arreglos de copia de seguridad; es una pregunta justa e importante.

## A dónde ir ahora

- ¿Te autoalojas? Configura copias de seguridad reales en @doc(selfHosting.backupAndRestore).
- Mover la configuración de un tipo entre cuentas se explica en @doc(collectionTypes.importExport).
- Ve qué más está planeado en la @doc(troubleshooting.featureStatus, "página de estado de funciones").
