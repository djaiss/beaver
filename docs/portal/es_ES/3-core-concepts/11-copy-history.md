---
id: copyHistory.concept
title: El historial de un ejemplar
slug: el-historial-de-un-ejemplar
section: conceptos-basicos
---

# El historial de un ejemplar

Esta página explica el corazón conceptual de KolleK: un ejemplar muestra su estado actual, mientras que todo lo que le ha ocurrido alguna vez vive en registros independientes y fechados. Entiende esto una vez y toda la sección de seguimiento se convierte en un conjunto de tareas evidentes.

## Estado actual frente a historial

Fíjate en uno de los relojes de Priya. El ejemplar te dice su estado actual de un vistazo: su @doc(conditions.overview, "estado") es Usado, su @doc(locations.overview, "ubicación") actual es la vitrina, su valor estimado es lo que dijo la última tasación.

Nada de eso se escribe como un simple dato que sobrescribe al anterior. Cada uno es la punta visible de un registro subyacente:

- El valor estimado es su **valoración más reciente**.
- El precio que pagó, y la fecha en que lo adquirió, provienen de su **transacción de adquisición más antigua**.
- La ubicación actual es la **entrada abierta en su historial de ubicaciones**.

El ejemplar es un resumen. Los registros son la verdad.

## Los tipos de registro

Siete tipos de registros fechados pueden colgar de un ejemplar, cada uno con su propio propósito y su propia página de instrucciones:

- Las **transacciones** registran cambios de dinero y de propiedad: lo que pagaste, por lo que vendiste, comisiones, envío. Consulta @doc(copies.recordPaymentsAndValue).
- Las **valoraciones** registran lo que valía el ejemplar en un momento dado, y quién lo dijo. La misma página que las transacciones, porque ambas se confunden con facilidad.
- Los **registros de seguro** capturan la cobertura: aseguradora, valor asegurado, fechas de la póliza. Consulta @doc(copies.insure).
- Los **préstamos** hacen seguimiento de la custodia cuando un ejemplar sale de tus manos o llega desde las de otra persona. Consulta @doc(loans.lendAndBorrow).
- Los **registros de mantenimiento** anotan los trabajos de limpieza, reparación y conservación. Consulta @doc(copies.recordMaintenance).
- Los **eventos de procedencia** construyen la historia de propiedad y autenticidad. Consulta @doc(copies.traceProvenance).
- El **historial de ubicaciones** recuerda cada lugar en el que ha vivido el ejemplar. Consulta @doc(copies.move, "Mueve un ejemplar").

También puedes @doc(copies.attachDocuments, "adjuntar documentos") (recibos, tasaciones, certificados) al ejemplar o a cualquier registro individual, y leerlo todo combinado en @doc(copyHistory.readTimeline, "la línea de tiempo del ejemplar").

## Las dos reglas que le dan coherencia

**El dinero solo vive en las transacciones.** Un precio de compra es una transacción. Una venta es una transacción. Las valoraciones y los eventos de procedencia describen valor e historia, nunca pagos.

**El historial es de solo adición.** Revalorar un ejemplar escribe una nueva valoración junto a la anterior. Renovar el seguro escribe un registro nuevo. Nada sobrescribe el pasado, y por eso la línea de tiempo puede contar toda la historia años después.

:::note
Si te encuentras editando un registro antiguo para reflejar algo nuevo, detente y añade un registro nuevo en su lugar. Editar es para corregir errores, no para actualizar la realidad.
:::

## ¿Necesitas todo esto?

No. Emma cataloga la mayoría de sus cómics con solo un ejemplar, un estado y una ubicación. Los registros de historial demuestran su valor en las piezas que importan: las valiosas, las aseguradas, las prestadas y las heredadas. Usa tanto o tan poco como merezca cada ejemplar.

## A dónde ir a continuación

- Empieza por el dinero en @doc(copies.recordPaymentsAndValue).
- Consulta toda la historia en una sola vista en @doc(copyHistory.readTimeline).
- Sigue una pieza valiosa de principio a fin en el tutorial @doc(tutorials.trackValuableItem, "Sigue toda la vida de un elemento valioso").
