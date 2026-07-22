---
id: copies.traceProvenance
title: Traza la procedencia de un ejemplar
slug: trazar-la-procedencia
section: historial-del-ejemplar
---

# Traza la procedencia de un ejemplar

La procedencia es la historia de dónde viene un ejemplar: quién lo ha poseído, dónde se ha exhibido, cuándo se autenticó y cómo llegó hasta ti. En piezas valiosas o históricamente interesantes, esa historia forma parte de su valor. KolleK te permite construirla como una secuencia de eventos de procedencia fechados que se lee, de más antiguo a más reciente, como una narración.

A diferencia de los demás registros de esta sección, la procedencia suele remontarse mucho antes de que poseyeras el ejemplar, a décadas que apenas conoces. El modelo está pensado para esa incertidumbre.

## Qué registra un evento de procedencia

Cada evento tiene un **tipo**, un **título** y todo el contexto del que dispongas: las **partes** implicadas, la **ubicación**, una **referencia** (un número de catálogo, un lote de subasta, una entrada de archivo) y una **fecha**.

Los tipos de evento cubren la vida de un objeto: **Adquisición**, **Venta**, **Regalo**, **Herencia**, **Transferencia de propiedad**, **Transferencia de custodia**, **Préstamo**, **Devolución**, **Exhibición**, **Autenticación**, **Tasación**, **Restauración significativa**, **Origen**, **Descubrimiento** y **Otro**.

Dos de ellos anclan los extremos de la historia. **Origen** registra dónde empezó el objeto (su fabricación, su impresión, su acuñación). **Descubrimiento** registra el momento en que salió a la luz, cuando eso es una historia en sí misma.

## Fechas de las que no estás seguro

Las fechas de procedencia suelen ser aproximadas, y fingir lo contrario distorsionaría la historia. Cada evento lleva una **precisión de fecha** junto a su fecha:

- **Fecha exacta**. Conoces el día.
- **Mes**. Conoces el mes y el año.
- **Año**. Solo conoces el año.
- **Aproximada**. Una estimación razonable. Léela como "hacia".
- **Desconocida**. El evento ocurrió, pero no puedes fecharlo.

El evento se muestra según su precisión, de modo que "hacia 1970" y "marzo de 1970" se ven tan seguros como realmente lo son.

## La regla del dinero

:::note
Los eventos de procedencia no llevan importes. El dinero siempre vive en las transacciones. Un evento ligado a una compra o venta se enlaza con su transacción en su lugar, así la narración y la contabilidad nunca se separan.
:::

Esta es la misma regla que viste en @doc(copies.recordPaymentsAndValue), aplicada desde el otro lado.

## Construye una historia de procedencia

El Omega Speedmaster de 1968 de Priya llegó con una carpeta de documentación de la casa de subastas. Ella reconstruye su historia.

::::steps
:::step title="Abre el historial del ejemplar"
Abre el elemento, ve a su pestaña **Historial**, selecciona el ejemplar y abre la sección **Procedencia**.

::screenshot{label="Pestaña Historial con la sección Procedencia abierta"}
:::

:::step title="Empieza por el origen"
Añade un evento de **Origen**: "Fabricado, Bienne, Suiza", fechado en 1968 con precisión de **Año**.
:::

:::step title="Añade lo que respalda la documentación"
Añade una **Transferencia de propiedad** para el primer propietario conocido, fechada de forma **Aproximada** a principios de los años setenta, con el nombre de la parte según los papeles de servicio. Añade un evento de **Autenticación** para el extracto del archivo del fabricante, con el número de extracto como **referencia**.
:::

:::step title="Termina con tu adquisición"
Añade un evento de **Adquisición** para su propia compra, fechado con exactitud, y enlázalo con la transacción de compra que ya registró. El precio vive en la transacción, no aquí.
:::
::::

Leída de arriba abajo, la sección cuenta ahora la historia del reloj desde el taller suizo hasta la colección de Priya.

## Verificado o leyenda familiar

Cada evento lleva un indicador de **verificado**, junto con una nota de cómo se verificó. Úsalo con honestidad. Un extracto de archivo es una prueba verificada. "Mi abuelo siempre decía que lo compró en Ginebra" también es una parte real de la historia, pero permanece sin verificar, y la narración sale reforzada por reconocer la diferencia.

## Eventos que llegan por sí solos

Parte de la procedencia se construye sola. Un @doc(loans.lendAndBorrow, "préstamo") marcado como parte de la procedencia añade los eventos de préstamo y devolución correspondientes, y un @doc(copies.recordMaintenance, "registro de mantenimiento") marcado como significativo aparece como un evento de restauración. Tú reconstruyes el pasado lejano; el presente se documenta a sí mismo a medida que ocurre.

## A dónde ir a continuación

- Adjunta el extracto de archivo o el certificado a su evento en @doc(copies.attachDocuments).
- Registra la compra a la que enlaza el evento de adquisición en @doc(copies.recordPaymentsAndValue).
- Lee la historia terminada en @doc(copyHistory.readTimeline).
