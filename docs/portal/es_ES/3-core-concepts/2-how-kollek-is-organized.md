---
id: kollek.howOrganized
title: Cómo se organiza KolleK
slug: como-se-organiza-kollek
section: conceptos-basicos
---

# Cómo se organiza KolleK

Esta página te da el mapa completo antes de entrar en detalle. Todo lo demás en esta sección hace zoom sobre una parte de él.

## La columna vertebral: cuatro niveles

Todo lo que catalogas en KolleK vive dentro de una jerarquía sencilla:

- Una **@doc(accounts.usersAndRoles, "cuenta")** es tu espacio de trabajo. Todo lo que hay debajo pertenece a exactamente una cuenta.
  - Una **@doc(collections.overview, "colección")** es un grupo con nombre de cosas, como "Mis cómics" o "Bodega de vinos".
    - Un **@doc(items.itemsVsCopies, "elemento")** es un tipo de cosa, como "Amazing Spider-Man #1".
      - Un **@doc(items.itemsVsCopies, "ejemplar")** es una instancia física concreta de ese elemento que realmente posees.

La cuenta de Emma contiene su colección "Mis cómics". Dentro de ella está el elemento "Amazing Spider-Man #1". Ella posee dos, así que el elemento tiene dos ejemplares, cada uno con su propio estado, su lugar de almacenamiento y su valor.

La separación entre elemento y ejemplar es el corazón del modelo, y tiene @doc(items.itemsVsCopies, "su propia página"). Si solo lees una página de conceptos, que sea esa.

## Las herramientas compartidas

Alrededor de la columna vertebral hay unas pocas herramientas a nivel de cuenta. Se definen una vez y se reutilizan en todas partes:

- Los **@doc(collectionTypes.overview, "tipos de colección")** deciden qué detalles registra cada tipo de elemento. Un tipo Cómics pide un número de emisión, un tipo Vino pide una añada.
- **@doc(organizing.categoriesSetsAndSeries, "Categorías, sets y series")** agrupan elementos de tres formas distintas: organizar dentro de una colección, hacer seguimiento de una lista finita hasta completarla, y conectar una franquicia a través de varias colecciones.
- Las **@doc(tags.overview, "etiquetas")** son etiquetas libres compartidas en toda la cuenta, como "Firmado".
- Las **@doc(locations.overview, "ubicaciones")** describen dónde viven físicamente los ejemplares, y se anidan: una caja en un estante en una habitación.
- Los **@doc(conditions.overview, "estados")** califican la condición de un ejemplar, de Nuevo a Dañado.

## La capa de historial

Cada ejemplar también lleva @doc(copyHistory.concept, "su propio historial"): lo que pagaste, lo que ha valido a lo largo del tiempo, su seguro, sus préstamos, su mantenimiento, su procedencia y cada lugar donde se ha guardado. El ejemplar muestra su estado actual, y los registros de historial cuentan la historia detrás de él.

## Para no perderte

:::note
Los detalles descriptivos viven en el elemento. Todo lo físico (estado, ubicación, dinero, historial) vive en el ejemplar. Si tienes dudas, pregúntate: "¿esto es cierto para cualquier ejemplar de esta cosa, o solo para este?".
:::

## A dónde ir a continuación

- Conoce el espacio de trabajo y a las personas que hay en él en @doc(accounts.usersAndRoles).
- Ve directamente a la idea clave en @doc(items.itemsVsCopies).
- ¿Prefieres hacer antes que leer? Prueba el @doc(gettingStarted.quickStart, "inicio rápido de cinco minutos").
