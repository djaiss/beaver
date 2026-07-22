---
id: collectionTypes.importExport
title: Importa y exporta un tipo de colección
slug: importa-y-exporta-un-tipo-de-coleccion
section: organizacion
---

# Importa y exporta un tipo de colección

Un @doc(collectionTypes.overview, "tipo de colección") bien construido merece compartirse. KolleK puede exportar la definición de un tipo como archivo JSON e importar uno de vuelta, así que puedes copiar una configuración entre cuentas, compartirla con otro coleccionista, o guardar una copia antes de reorganizarla.

Necesitas el rol de editor o propietario.

## Qué se mueve y qué no

La exportación contiene solo la definición del tipo: su nombre, su color, sus grupos de campos, sus campos personalizados y las opciones de cualquier campo de selección.

:::note
Exportar un tipo no exporta los elementos ni sus datos. Actualmente no existe importación o exportación de elementos ni de colecciones completas. Consulta la @doc(troubleshooting.featureStatus, "página de estado de funciones") para ver en qué punto está eso, y @doc(dataSafety.backupCollectionData) para conocer qué portabilidad existe hoy.
:::

## Exporta un tipo

::::steps
:::step title="Abre el tipo"
En los ajustes de la cuenta, abre **Tipos de colección** y selecciona el tipo que quieres exportar.
:::

:::step title="Expórtalo"
Elige **Exportar**. KolleK descarga un archivo JSON que describe el tipo.

::screenshot{label="Editor de tipos con la opción de exportar"}
:::
::::

El archivo es texto plano. Puedes leerlo, guardarlo con tus copias de seguridad, o enviarlo a alguien.

## Importa un tipo

La importación funciona a partir de JSON pegado, así que primero abre el archivo que recibiste en cualquier editor de texto y copia su contenido.

::::steps
:::step title="Inicia la importación"
En los ajustes de la cuenta, abre **Tipos de colección** y elige **Importar**.
:::

:::step title="Pega el JSON"
Pega la definición del tipo en el campo y confirma. KolleK la valida y crea el tipo con sus grupos, campos y opciones.

::screenshot{label="Formulario de importación con JSON pegado"}
:::

:::step title="Revisa el resultado"
Abre el tipo nuevo y comprueba que los campos llegaron como esperabas, luego vincúlalo a una colección para empezar a usarlo.
:::
::::

## Un ejemplo práctico

El amigo de Noah también colecciona vinilos y ha perfeccionado un tipo "Discos de vinilo" con un conjunto agrupado de campos: información de lanzamiento (artista, álbum, año de lanzamiento) y detalles de prensado (prensado, velocidad, vinilo de color). En lugar de reconstruirlo a mano, Noah le pide la exportación, pega el JSON en su propia cuenta, y tiene la estructura idéntica en segundos.

Si quieres ver el formato exacto que espera el importador, exporta primero cualquier tipo existente, como el tipo Cómics ya preparado, y úsalo como plantilla. Tus propias exportaciones siempre se importan sin problemas.

## A dónde ir después

- Perfecciona el tipo importado en @doc(collectionTypes.setup).
- Entiende qué más se puede y no se puede exportar en @doc(dataSafety.backupCollectionData).
