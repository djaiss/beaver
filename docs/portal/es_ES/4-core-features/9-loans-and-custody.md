---
id: loans.custody
title: Préstamos y custodia
slug: prestamos-y-custodia
section: funciones-principales
---

# Préstamos y custodia

Un préstamo es un traspaso temporal de la custodia sin ningún traspaso de la propiedad. Cuando prestas una pieza a un amigo, a una galería o a un museo, sigue siendo tuya. Cuando tomas prestada una pieza, sigue perteneciendo a otra persona. La sección **Préstamos** es la vista de todo el conjunto de la cuenta de aquello que en este momento está fuera de tus manos o en tus manos en calidad de préstamo.

Consultar la sección está abierto a cualquier rol. Registrar, devolver, editar y eliminar un préstamo requiere el rol de **editor** o **propietario**.

## Las dos direcciones

Cada préstamo apunta en uno de dos sentidos, y la sección muestra una dirección a la vez. Usa el conmutador de la parte superior para alternar entre ambas.

- **Prestado.** Una pieza tuya que otra persona tiene en su poder. Mientras un préstamo saliente está activo o vencido, su ejemplar aparece como **Prestado** en tu colección, porque no está físicamente contigo.
- **Tomado en préstamo.** Una pieza que pertenece a otra persona y que tú tienes por ahora. Una pieza tomada en préstamo nunca cambia cómo aparecen tus propios ejemplares, porque nunca fue tuya.

## Qué muestran las pestañas

Dentro de una dirección, las pestañas dividen los mismos préstamos de distintas maneras.

- **Todos los préstamos.** Cada préstamo de la dirección, con un cuadro de búsqueda y filtros por colección, estado y orden de clasificación.
- **Vencimientos y vencidos.** Tres listas: préstamos que han pasado su fecha de vencimiento, préstamos que vencen dentro de treinta días, y préstamos sin plazo que no tienen ninguna fecha de vencimiento.
- **Riesgos y excepciones.** Los préstamos que necesitan una segunda mirada: vencidos, perdidos, devueltos en peor estado, sin fecha de vencimiento, sin estado registrado a la salida, o prestados sin ningún documento en el expediente.
- **Por parte.** Una ficha por cada persona o institución, para que puedas ver de una vez todo lo que tiene un mismo prestatario o prestamista.
- **Depósitos.** Lo que retienes o lo que se te debe en el conjunto de los préstamos abiertos, y los préstamos que llevan un depósito.
- **Cronología.** Próximos vencimientos, piezas devueltas recientemente y piezas prestadas recientemente.

Los mosaicos de estadísticas de la parte superior son atajos: cada uno abre la pestaña que responde a esa cifra.

## Registrar un préstamo

Puedes iniciar un préstamo directamente desde la sección, sin tener que buscar antes el ejemplar.

::::steps
:::step title="Abrir el panel de nuevo préstamo"
Elige **Nuevo préstamo**. Selecciona la dirección y luego desciende desde la colección hasta el objeto, hasta el ejemplar exacto que se mueve.
:::

:::step title="Nombrar la parte y las fechas"
Indica a quién va la pieza o de quién procede, la fecha en que salió y una fecha de vencimiento. Marca **sin plazo** cuando no haya una fecha de devolución acordada.
:::

:::step title="Registrar el estado y cualquier depósito"
Elige el **estado a la salida** para que una devolución posterior pueda compararse con él, y registra un **depósito** si cambió de manos alguna cantidad. La moneda del depósito toma por defecto la de la colección.
:::

:::step title="Márcalo para la procedencia si forma parte de la historia"
Marca **incluir en la procedencia** para un préstamo institucional o una exposición, y se genera un evento de procedencia correspondiente. Déjalo sin marcar para un préstamo personal informal, que permanece solo en el historial de préstamos.
:::
::::

### Un solo préstamo abierto por ejemplar

Un ejemplar físico solo puede estar en un lugar a la vez, así que un ejemplar puede tener como máximo un préstamo **saliente abierto**. Si intentas prestar un ejemplar que ya está fuera, la sección lo bloquea y te pide que devuelvas antes el préstamo actual. Esta regla también rige en la API JSON.

## Devolver un préstamo

Cerrar un préstamo es un paso propio, no una edición, para que capture lo que una edición no captaría.

::::steps
:::step title="Abrir el préstamo y marcarlo como devuelto"
Abre el préstamo desde cualquier lista y luego elige **Marcar como devuelto**.
:::

:::step title="Registrar la devolución"
Indica la fecha en que volvió la pieza y el **estado a la entrada**. Fijar un estado a la entrada actualiza el estado actual del ejemplar y devuelve el ejemplar a tu custodia.
:::
::::

Cuando el estado a la entrada es peor que el estado a la salida, el préstamo se marca como posible daño, tanto en el propio préstamo como en la lista de riesgos **Devueltos en peor estado**.

## Exportar lo que está fuera

El botón **Exportar lo que está fuera** descarga un CSV de los préstamos abiertos en la dirección actual, para que dispongas de una lista clara de lo que en este momento está en manos de otra persona, o en las tuyas.

## Relacionado

- Los préstamos también aparecen en el historial propio de un ejemplar. Consulta @doc(copies.track) para el registro del ejemplar del que dependen.
