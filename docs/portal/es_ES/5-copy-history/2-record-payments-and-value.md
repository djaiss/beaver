---
id: copies.recordPaymentsAndValue
title: Registra lo que pagaste y lo que vale
slug: registrar-pagos-y-valor
section: historial-del-ejemplar
---

# Registra lo que pagaste y lo que vale

El dinero y el valor son las dos preguntas que más se hacen los coleccionistas, y KolleK las mantiene deliberadamente separadas. Una **transacción** registra dinero que realmente cambia de manos. Una **valoración** registra lo que vale un ejemplar en un momento dado, se haya movido dinero o no. Esta página te muestra cómo registrar ambas cosas y explica la regla que las mantiene separadas.

Si no has leído @doc(copyHistory.concept, "El historial de un ejemplar explicado"), léelo primero. Presenta la idea de que estos registros son un historial de solo adición, no campos que se sobrescriben.

## La regla que lo mantiene todo claro

Un precio de compra es una transacción, no una valoración.

Cuando Priya compra un Omega Speedmaster de 1968 por 4.200, eso es una transacción de tipo **Compra**. Registra lo que pagó ese día, y eso no cambia nunca. Lo que *vale* el reloj es una pregunta distinta que cambia con el tiempo, y cada respuesta es su propia valoración.

KolleK deriva automáticamente dos cifras de estos registros:

- El **valor estimado** de un ejemplar es el importe de su valoración más reciente. Un ejemplar sin valoraciones aparece como sin valorar, no como si valiera cero.
- El **precio pagado** y la **fecha de adquisición** de un ejemplar provienen de su transacción de adquisición más antigua (una Compra, un Intercambio, un Regalo recibido o una Herencia).

Nunca escribes estas cifras directamente en el ejemplar. Registras el historial, y los números actuales se derivan de él.

## Registra una transacción

Una transacción cubre cualquier movimiento de dinero o de propiedad relacionado con un ejemplar: comprarlo, venderlo, intercambiarlo, pagar una comisión o enviarlo a algún sitio.

::::steps
:::step title="Abre el historial del ejemplar"
Abre el elemento, ve a su pestaña **Historial** y selecciona el ejemplar que quieres. Después abre la sección **Transacciones**.

::screenshot{label="Pestaña Historial con la sección Transacciones abierta"}
:::

:::step title="Añade una transacción"
Elige añadir una transacción y selecciona su **tipo**: Compra, Venta, Intercambio, Regalo recibido, Regalo entregado, Herencia, Reembolso, Comisión, Impuesto, Envío u Otro.
:::

:::step title="Introduce el importe"
Rellena el **importe** y, opcionalmente, los **impuestos**, las **comisiones** y el **envío**, para que quede registrado el coste real total y no solo el precio de etiqueta.
:::

:::step title="Añade el contexto"
Registra la **contraparte** (a quién compraste o a quién vendiste), la **fecha** y una **referencia**, como un número de pedido o de lote de subasta. Guarda la transacción.
:::
::::

Priya registra la compra de su Speedmaster: tipo **Compra**, importe 4.200, comisiones 120 para la casa de subastas, contraparte "Fine Time Auctions" y el número de lote como referencia. Ese único registro ya responde qué pagó, cuándo lo adquirió y de dónde vino.

:::note
La transacción de adquisición más antigua (Compra, Intercambio, Regalo recibido o Herencia) es la que da al ejemplar su fecha de adquisición. Los ejemplares que no tienen ninguna se cuentan como sin fecha en tus estadísticas, así que regístrala incluso para cosas que compraste hace mucho, con tu mejor estimación de la fecha.
:::

## Registra una valoración

Una valoración responde a "cuánto vale esto ahora mismo, y con qué grado de seguridad lo sé".

::::steps
:::step title="Abre la sección Valoraciones"
Desde la misma pestaña **Historial**, con tu ejemplar seleccionado, abre la sección **Valoraciones**.
:::

:::step title="Añade una valoración"
Elige un **tipo de valoración**: Estimación propia, Tasación profesional, Estimación de mercado, Valor de seguro, Estimación de subasta, Estimación automática u Otro.
:::

:::step title="Introduce el valor y tu grado de confianza"
Rellena el **importe**, elige un nivel de **confianza** (Baja, Media, Alta o Desconocida) y registra **quién lo valoró**. Guárdalo.

::screenshot{label="Formulario de nueva valoración con tipo, importe y confianza"}
:::
::::

Dos años después, un anticuario le dice a Priya que el Speedmaster podría alcanzar unos 5.500. Ella añade una nueva valoración: **Estimación de mercado**, 5.500, confianza **Media**, valorado por el anticuario. Su valoración original permanece en el historial, y el valor estimado del ejemplar se actualiza a la nueva cifra.

:::note
Revalorar siempre implica escribir una nueva valoración. Nunca editas la anterior para ponerle una cifra nueva, así conservas un registro auténtico de cómo evolucionó el valor con el tiempo. Ese historial es lo que dibuja el gráfico de valor a lo largo del tiempo en tus estadísticas.
:::

## Dónde aparecen estas cifras

Las cifras que registras aquí alimentan el resto de KolleK: el valor total mostrado en cada colección, los gráficos de valor a lo largo del tiempo y de adquisiciones en las @doc(insights.collectionStatistics, "estadísticas de la colección"), y los elementos principales por valor. Unas transacciones y valoraciones completas son lo que hace fiables esas pantallas.

## A dónde ir a continuación

- Guarda la documentación junto al registro. @doc(copies.attachDocuments), como el recibo de una transacción o la tasación de una valoración.
- ¿Vas a asegurar el ejemplar por ese valor? @doc(copies.insure).
- ¿Quieres construir la historia completa de propiedad? @doc(copies.traceProvenance).
