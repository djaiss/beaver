---
id: collectionTypes.overview
title: Tipos de colección y campos personalizados
slug: tipos-de-coleccion-y-campos-personalizados
section: conceptos-basicos
---

# Tipos de colección y campos personalizados

Los cómics necesitan un número de emisión. El vino necesita una añada. Los relojes necesitan un movimiento. KolleK no adivina qué coleccionas, te deja definirlo. Esta página explica las piezas: tipos, campos personalizados y grupos de campos.

## Tipos de colección

Un **tipo de colección** describe un tipo de cosa que coleccionas: Cómics, Vinilos, Vino. Es el contenedor de los campos personalizados que tienen sentido para ese tipo de cosa.

Los tipos son de ámbito de cuenta y reutilizables. Define un tipo Cómics una vez, y cualquier @doc(collections.overview, "colección") de tu cuenta podrá activarlo. Una colección puede activar varios tipos a la vez, lo cual encaja bien con colecciones mixtas: la colección "Música" de Noah activa tanto Vinilos como CD, así cada elemento puede catalogarse como uno u otro.

Cuando se asigna un tipo a un elemento, su formulario incorpora los campos personalizados que define ese tipo.

## Campos personalizados

Un **campo personalizado** es un detalle que pide un tipo. Cada campo tiene su propio tipo de dato:

- **Texto**, para cualquier cosa de formato libre, como Editorial o Artista.
- **Número**, para el Número de emisión o el Año de lanzamiento.
- **Fecha**, para una fecha de portada.
- **Sí / No**, para Firmado o Primera edición.
- **Selección**, un desplegable con opciones que tú defines, como un Grado de PSA 10, PSA 9 o Sin gradar.
- **Valoración**, hasta cinco estrellas, para tu propia "Mi valoración".

Los valores se registran por elemento. El "Amazing Spider-Man #1" de Emma tiene Número de emisión 1 y Editorial Marvel; sus otros cómics comparten los mismos campos con sus propios valores.

## Grupos de campos

Cuando un tipo tiene muchos campos, los **grupos de campos** mantienen el formulario legible. Un grupo es simplemente una sección con nombre: el tipo Cómics ya incluido agrupa sus campos bajo "Información editorial" y "Estado y gradación". Los formularios largos se leen como secciones ordenadas en lugar de una lista interminable.

## Los tipos ya incluidos

Una cuenta recién creada incluye una docena de tipos ya definidos para que no empieces desde cero: Cómics, Cartas coleccionables, Vinilos, CD, DVD, Monedas, Sellos, Libros, Figuras de acción / Juguetes, Videojuegos, Relojes y Vino, cada uno con campos razonables ya agrupados. Úsalos tal cual, ajústalos, o ignóralos y crea los tuyos propios.

:::note
Los tipos describen elementos, no ejemplares. Un campo que varía en cada pieza física que posees, como el estado o un número de serie, pertenece al ejemplar en su lugar. Consulta @doc(items.itemsVsCopies).
:::

## A dónde ir a continuación

- Crea o ajusta un tipo paso a paso en @doc(collectionTypes.setup).
- Comparte la definición de un tipo con alguien en @doc(collectionTypes.importExport).
- Consulta los campos en acción en @doc(items.addAndEdit).
