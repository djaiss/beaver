---
id: locations.overview
title: Ubicaciones
slug: ubicaciones
section: conceptos-basicos
---

# Ubicaciones

Una ubicación responde a la pregunta que toda colección en crecimiento acaba haciéndose: "¿dónde lo puse?". Esta página explica cómo modela KolleK el almacenamiento físico.

## Qué es una ubicación

Una ubicación es un lugar donde vive físicamente un @doc(items.itemsVsCopies, "ejemplar"): una habitación, un estante, una caja, una caja fuerte. Cada ubicación puede llevar un emoji para que se reconozca de un vistazo en las listas.

Las ubicaciones se anidan tanto como necesites, así pueden reflejar tu espacio real. Noah modela el suyo: Salón, luego Estante A dentro de él, luego Caja 3 dentro de ese. Cuando se pregunta dónde está un disco, la respuesta es tan precisa como su mapa.

Las ubicaciones son de ámbito de cuenta. Define "Vitrina" una vez y todas las colecciones podrán guardar ejemplares ahí, lo cual coincide con la realidad: un mismo estante puede tener cómics y monedas uno junto al otro.

## Las ubicaciones se asocian a ejemplares, no a elementos

Un elemento es una idea, así que es el ejemplar el que está en algún sitio. Los dos ejemplares del mismo cómic que tiene Emma viven en lugares distintos: uno en la Caja larga 1, otro enmarcado en la pared. Cada ejemplar apunta a su propia ubicación actual.

Una cuenta recién creada viene con algunas ubicaciones iniciales (Salón, Almacén, Vitrina, Garaje, Oficina). Renómbralas, anida cosas dentro de ellas, o sustitúyelas por las tuyas propias.

## Los traslados se recuerdan

Cuando mueves un ejemplar, KolleK no se limita a sobrescribir la ubicación anterior. Registra el traslado, así el ejemplar conserva un rastro de todos los sitios en los que ha estado y cuándo. La ubicación actual es simplemente la última entrada de ese rastro. Esto forma parte de @doc(copyHistory.concept, "el historial de un ejemplar"), y las instrucciones están en @doc(copies.move).

## A dónde ir a continuación

- Construye tu mapa de almacenamiento en @doc(locations.setup).
- Traslada las cosas correctamente en @doc(copies.move).
- Consulta dónde aparecen las ubicaciones en el formulario del ejemplar en @doc(copies.track).
