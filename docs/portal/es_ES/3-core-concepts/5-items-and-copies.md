---
id: items.itemsVsCopies
title: Elementos frente a ejemplares
slug: elementos-y-ejemplares
section: conceptos-basicos
---

# Elementos frente a ejemplares

Esta es la página más importante de la documentación. La diferencia entre un elemento y un ejemplar es la idea que hace que KolleK sea distinto de una simple lista, y casi todas las demás páginas dan por hecho que la conoces. Se aprende en dos minutos.

## La distinción

Un **elemento** es el *tipo de cosa*. Un **ejemplar** es *una instancia física concreta que realmente posees*.

"Amazing Spider-Man #1" es un elemento. El ligeramente desgastado que Emma tiene en su caja larga es un ejemplar. El que compró en subasta, casi en estado de nuevo, es otro ejemplar. Mismo elemento, dos ejemplares.

- ¿Posees tres unidades del mismo cómic? Eso es **un elemento con tres ejemplares**.
- Cada ejemplar tiene su propio @doc(conditions.overview, "estado"), su propia @doc(locations.overview, "ubicación") de almacenamiento, su propio valor y su propio @doc(copyHistory.concept, "historial").
- El elemento contiene todo lo que los ejemplares tienen en común: el nombre, la descripción, las fotos, los valores de los campos personalizados, las etiquetas.

## La regla que hay que recordar

Los detalles descriptivos y de clasificación viven en el **elemento**. Todo lo relacionado con el estado, la ubicación, el dinero y el historial vive en el **ejemplar**.

Pregúntate: "¿esto sería cierto para cualquier ejemplar de esta cosa?". El guionista del cómic es el mismo para todos los ejemplares, así que pertenece al elemento. Lo que pagaste es distinto para cada uno, así que pertenece al ejemplar.

## Un ejemplo trabajado

Priya cataloga un Omega Speedmaster de 1968:

- El **elemento** lleva el nombre, una descripción, fotos y campos personalizados como Marca, Modelo y Movimiento.
- Su primer **ejemplar** está calificado como Usado, vive en su vitrina, y lleva el precio que pagó en 2019 más una tasación profesional.
- Su segundo **ejemplar**, heredado de su abuelo, está calificado como Desgastado, vive en una caja fuerte, y lleva un registro de seguro y un rastro de procedencia que se remonta a 1970.

Un reloj como concepto, dos relojes físicos muy distintos, cada uno con seguimiento completo.

## Qué registra un ejemplar

Además del estado y la ubicación, un ejemplar lleva un identificador opcional (un número de serie o de certificación), un estatus, una cantidad, una nota y un valor estimado. El estatus cubre toda la vida de un ejemplar: En posesión, Encargado, Prestado, Vendido, Regalado, Perdido, Robado, Desechado u Otro. Los detalles están en @doc(copies.track).

Lo que pagaste y lo que vale un ejemplar no se escriben directamente en el ejemplar. Provienen de sus transacciones y valoraciones, que forman parte de @doc(copyHistory.concept, "el historial de un ejemplar").

## El error que hay que evitar

:::note
Dos unidades de la misma cosa son dos ejemplares de un elemento, nunca dos elementos. Si estás a punto de crear "Amazing Spider-Man #1 (el segundo)", detente y añade un ejemplar al elemento existente en su lugar.
:::

Los elementos duplicados dividen tu historial y tus estadísticas. Un elemento con varios ejemplares mantiene el catálogo ordenado y permite que cada pieza física cuente su propia historia.

## A dónde ir a continuación

- Registra tus ejemplares en @doc(copies.track).
- Descubre qué puede recordar un ejemplar en @doc(copyHistory.concept).
- Registra el dinero correctamente en @doc(copies.recordPaymentsAndValue).
