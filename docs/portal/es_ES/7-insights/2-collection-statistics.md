---
id: insights.collectionStatistics
title: Entiende las estadísticas de tu colección
slug: estadisticas-de-la-coleccion
section: analisis
---

# Entiende las estadísticas de tu colección

Cada colección tiene una pantalla de estadísticas que convierte tu introducción de datos en respuestas: cuánto vale, cómo ha crecido y dónde se concentra el valor. Esta página explica cada cifra y, con la misma importancia, de dónde sale cada número, para que puedas confiar en lo que lees.

## De dónde salen los números

Dos reglas gobiernan casi todo en esta pantalla. Provienen de @doc(copyHistory.concept, "cómo funciona el historial de un ejemplar"):

- **El valor actual de un ejemplar es su @doc(copies.recordPaymentsAndValue, "valoración") más reciente.** Un ejemplar que nunca se ha valorado se cuenta como sin valorar, no como si valiera cero por suposición.
- **La fecha de adquisición de un ejemplar proviene de su @doc(copies.recordPaymentsAndValue, "transacción") de adquisición más antigua**, como una compra, un intercambio, un regalo recibido o una herencia. Un ejemplar sin ninguna transacción de ese tipo no tiene fecha de adquisición, así que no puede aparecer en los gráficos basados en el tiempo. La pantalla te indica cuántos ejemplares están sin fecha para que sepas qué les falta a los gráficos.

Si un gráfico se ve más vacío de lo que sientes que es tu colección, eso es la estadística invitándote a introducir más datos, no un fallo.

## Los totales

En la parte superior: el **número de elementos**, el **número de ejemplares**, el **valor estimado total** (la suma del valor actual de cada ejemplar) y el **valor medio por elemento**. También verás qué ha cambiado recientemente: elementos añadidos este mes y valor añadido este mes.

## Finalización de sets

Si la colección tiene @doc(sets.trackCompletion, "sets con un recuento objetivo"), la pantalla los agrega: cuántas piezas posees frente al objetivo combinado, y el porcentaje de finalización. Solo participan los sets con un objetivo mayor que cero. Un set que contiene más piezas que su objetivo se cuenta como completo, no como "más que completo".

## Valor a lo largo del tiempo

Un gráfico de doce meses del valor estimado acumulado de tu colección, mes a mes. Cada ejemplar se une a la línea en su fecha de adquisición, con su valor actual. Todo lo adquirido antes de la ventana de doce meses ya está incluido en el primer punto, así que la línea empieza desde tu total real, no desde cero.

## Adquisiciones por mes

Cuántos ejemplares adquiriste en cada uno de los últimos doce meses, calculado a partir de las mismas fechas de adquisición. Un gráfico plano aquí suele indicar transacciones de adquisición que faltan, más que un año tranquilo.

## Desgloses

- **Por categoría.** Cómo se reparten los elementos entre tus @doc(categories.organizeItems, "categorías"). Se nombran las seis categorías más grandes, el resto se agrupa en "Otras", y los elementos sin categorizar se muestran como su propio segmento.
- **Por estado.** Cómo se califican tus ejemplares, en recuentos y porcentajes por @doc(conditions.overview, "estado").
- **Valor por ubicación.** El valor sumado de los ejemplares en cada @doc(locations.overview, "ubicación"), así sabes qué hay en cada sitio. Priya usa esto para ver cuánto valor vive en su vitrina frente a su caja fuerte. Solo aparecen las ubicaciones que contienen valor.

## Elementos principales

Los cinco elementos más valiosos de la colección, ordenados por el valor actual combinado de sus ejemplares, cada uno mostrado con el estado y la ubicación de su ejemplar más valioso.

## A dónde ir a continuación

- Alimenta los gráficos: @doc(copies.recordPaymentsAndValue).
- Haz un seguimiento correcto de la finalización: @doc(sets.trackCompletion).
- Consulta la vista de toda la cuenta: @doc(insights.dashboard).
