---
id: copies.track
title: Registra los ejemplares que posees
slug: registra-los-ejemplares-que-posees
section: funciones-principales
---

# Registra los ejemplares que posees

Un elemento por sí solo es solo una descripción. Un **ejemplar** es tu registro de una instancia física concreta que de verdad posees, con su propio estado, ubicación, situación e historial. Esta página cubre añadir ejemplares y cada campo de un ejemplar.

La idea detrás de esta división se explica en @doc(items.itemsVsCopies). Añadir ejemplares requiere el rol de **editor** o **propietario**.

## Añade un ejemplar

Los ejemplares se añaden en el formulario del elemento, en línea, para que puedas registrarlos mientras catalogas.

::::steps
:::step title="Abre el elemento"
Abre el elemento y elige editarlo, luego añade un **ejemplar**.
:::

:::step title="Registra su estado físico"
Elige su **estado** de la lista y elige la **ubicación** donde se guarda.

::screenshot{label="Fila de ejemplar, campos de estado y ubicación"}
:::

:::step title="Configura su situación y detalles"
Deja la **situación** en Poseído para algo que tienes, o elige otra situación. Rellena cualquier otro campo que se aplique, luego guarda el elemento.
:::
::::

¿Tienes dos del mismo elemento? Añade un segundo ejemplar al mismo elemento, nunca un segundo elemento. Cada ejemplar mantiene su propio estado, ubicación e historial.

## Los campos del ejemplar

- **Identificador.** Un número de serie, un número de cápsula o cualquier marca que fije exactamente este ejemplar. Priya registra el número de serie grabado en cada uno de sus relojes.
- **@doc(conditions.overview, "Estado").** La calificación de este ejemplar, elegida de la lista ya preparada (Nuevo, Como nuevo, Usado, Desgastado, Dañado, más cualquiera que tu cuenta haya añadido).
- **@doc(locations.overview, "Ubicación").** Dónde vive actualmente el ejemplar. Cambiarla más tarde mediante un movimiento conserva el historial; consulta @doc(copies.move, "Mueve un ejemplar").
- **Situación.** En qué punto de su ciclo de vida está el ejemplar. Consulta la lista de abajo.
- **Cantidad.** Para ejemplares idénticos e intercambiables que no necesitas distinguir, como diez del mismo sobre de cartas sin abrir. Si cada ejemplar importa individualmente, dale a cada uno su propia fila en su lugar.
- **Fecha de baja.** Cuándo dejó el ejemplar tus manos, para situaciones como Vendido o Desechado.
- **Nota.** Cualquier cosa que merezca la pena recordar sobre este ejemplar en concreto.
- **Valor estimado.** Una cifra rápida de lo que vale el ejemplar. Por debajo, se guarda como una @doc(copies.recordPaymentsAndValue, "valoración") de tipo "Estimación propia", que abre el historial de valor del ejemplar en lugar de quedarse en el propio ejemplar. Para cualquier cosa que te importe de verdad, añade allí valoraciones fechadas correctamente.

## El ciclo de vida de la situación

- **Poseído.** En tu posesión. El predeterminado.
- **Pedido.** Comprado pero aún no ha llegado.
- **Prestado.** Con otra persona, pero sigue siendo tuyo. Se movió la custodia, no la propiedad, así que el ejemplar sigue contando como en posesión. Los préstamos se registran mejor mediante @doc(loans.lendAndBorrow), que configura esto por ti.
- **Vendido, Regalado.** La propiedad pasó a otra persona.
- **Perdido, Robado.** Desaparecido sin tu consentimiento.
- **Desechado.** Tirado o reciclado.
- **Otro.** Cualquier cosa que la lista no cubra.

Poseído, Pedido y Prestado cuentan como "todavía en posesión". Los demás registran ejemplares que han salido de la colección pero cuyo historial quieres conservar.

## Dónde vive el dinero

Puede que notes que no hay un campo "precio pagado" en el ejemplar. Es deliberado. Lo que pagaste, y cuándo adquiriste el ejemplar, viene de sus **transacciones**, y lo que vale a lo largo del tiempo viene de sus **valoraciones**. Esto conserva la historia completa del dinero en lugar de un único número que se sobrescribe. Empieza con @doc(copies.recordPaymentsAndValue).

## A dónde ir después

- Entiende los registros que puede llevar un ejemplar: @doc(copyHistory.concept, "El historial de un ejemplar explicado").
- Registra la compra: @doc(copies.recordPaymentsAndValue).
- Mantén su dirección al día: @doc(copies.move).
