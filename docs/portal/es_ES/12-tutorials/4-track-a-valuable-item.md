---
id: tutorials.trackValuableItem
title: "Tutorial: Sigue toda la vida de un elemento valioso"
slug: sigue-toda-la-vida-de-un-elemento-valioso
section: tutoriales
---

# Tutorial: Sigue toda la vida de un elemento valioso

La mayoría de los elementos necesitan un estado, una ubicación y, quizá, un precio. Uno realmente valioso merece más: prueba de lo que pagaste, una opinión profesional de su valor, un seguro, la documentación que respalda todo eso, y un registro de todos los sitios por los que pasa y todo lo que se le ha hecho. KolleK registra cada una de esas cosas como su propia entrada fechada en el ejemplar, y este tutorial las pone en práctica todas sobre una sola pieza.

Seguiremos a Priya, que acaba de comprar el mejor reloj de su colección, un cronógrafo de 1968. Al final, su ejemplar llevará una transacción, una valoración, un registro de seguro, dos documentos, un préstamo completado, un registro de mantenimiento y una narrativa de procedencia, todo legible como una sola línea de tiempo.

Este es el tutorial más largo. Hazlo con un elemento real tuyo, o simplemente léelo para ver cómo encajan las piezas.

## Antes de empezar

- Termina primero @doc(tutorials.catalogueFirstCollection, "Cataloga tu primera colección de principio a fin"). Este tutorial da por hecho que el ciclo básico ya es algo natural para ti.
- Lee @doc(copyHistory.concept, "El historial de un ejemplar explicado"). Es el mapa de todo lo que sigue.
- Recuerda las dos reglas que mantienen coherente el modelo: el dinero solo vive en transacciones, y revalorar o volver a asegurar crea un registro nuevo en lugar de sobrescribir el antiguo.

## Paso 1: Cataloga el elemento y su ejemplar

Priya crea el elemento "Heuer Carrera 2447" en su colección Watches, que usa el tipo predefinido **Watches**. Rellena los campos del tipo: **Brand**, **Model**, **Movement** (Automatic, Quartz o Manual), y responde **Box & Papers** con sí.

Luego añade el ejemplar, y hay un campo que aquí importa más de lo habitual:

- **Identifier.** Introduce el número de serie del reloj. Para elementos valiosos, esto es lo que vincula tu registro al objeto físico, igual que un número de slab para un cómic tasado.
- **Estado** y **ubicación**, como siempre.

Todo lo que sigue ocurre en la pestaña **Historial** de este ejemplar, que muestra un ejemplar a la vez.

## Paso 2: Registra la adquisición

::::steps
:::step title="Añade la transacción de compra"
En el historial del ejemplar, añade una **transacción** de tipo **Purchase**. Priya introduce el importe, la casa de subastas como **contraparte**, la **fecha**, la prima del comprador en **comisiones**, y el número de lote como **referencia**.

::screenshot{label="Formulario de transacción rellenado para una compra en subasta"}
:::
::::

Por qué esto importa: este único registro da al ejemplar su precio pagado y su fecha de adquisición, ancla las estadísticas, y más adelante anclará la narrativa de procedencia. Acértalo y todo lo demás depende de él. Los detalles están en @doc(copies.recordPaymentsAndValue).

## Paso 3: Añade una valoración profesional

Priya hace tasar el reloj. Añade una **valoración** con el tipo **Professional appraisal**, el importe tasado, la confianza en **High**, y el nombre del tasador como quien la valoró.

:::note
El año que viene lo hará tasar de nuevo y añadirá una nueva valoración. La antigua se queda. El valor estimado del ejemplar es siempre su valoración más reciente, y la secuencia de valoraciones es cómo algún día podrás representar su valor a lo largo del tiempo.
:::

## Paso 4: Asegúralo

Con una tasación profesional en la mano, el seguro es el siguiente paso obvio. Priya añade un @doc(copies.insure, "registro de seguro"): el **proveedor**, el **valor asegurado**, el **número de póliza**, el **tipo de cobertura**, la **franquicia**, las **fechas de inicio y fin**, si es un **elemento incluido en la póliza**, y los datos de contacto de la aseguradora. Deja el estado en **Active**.

Cuando la póliza se renueve, añadirá un registro nuevo y marcará este como **Expired**. Los registros expirados o cancelados siguen visibles como historial atenuado detrás del actual, que es exactamente lo que quieres cuando una reclamación pregunta qué cobertura existía en un año determinado.

## Paso 5: Adjunta la documentación

Los registros son afirmaciones. Los documentos son la prueba. Priya escanea dos papeles y los @doc(copies.attachDocuments, "adjunta") donde corresponde:

::::steps
:::step title="Adjunta el recibo a la transacción"
En la transacción de compra, adjunta la factura de la subasta como documento de tipo **Receipt**, con su fecha de emisión y el número de factura como referencia.
:::

:::step title="Adjunta la tasación a la valoración"
En la valoración, adjunta el informe del tasador como documento de tipo **Appraisal**.
:::
::::

Un documento puede ser un archivo subido (PDF, imágenes, Word, Excel, CSV o texto plano, hasta 12 MB) o un enlace externo si la documentación vive en otro sitio. Adjuntar cada documento al registro que prueba, en lugar de dejarlo suelto en el ejemplar, es lo que hace que la historia sea auditable más adelante.

## Paso 6: Préstalo para una exposición, y recupéralo

Una sociedad de horología local pide exponer el reloj durante un mes. La custodia es exactamente lo que registran los @doc(loans.lendAndBorrow, "préstamos").

::::steps
:::step title="Registra el préstamo saliente"
Priya crea un **préstamo** con dirección **Lent out**, la sociedad como la otra parte, "Exhibition" como propósito, las fechas de préstamo y devolución, y el estado del reloj al salir de sus manos.
:::

:::step title="Observa el cambio de estado del ejemplar"
Mientras el préstamo está abierto, el ejemplar se muestra como prestado. Sigue siendo suyo; lo que cambia es la custodia, no la propiedad. Si la fecha de devolución pasara sin que volviera, KolleK marcaría el préstamo como vencido automáticamente.
:::

:::step title="Registra la devolución"
Cuando el reloj vuelve, ella registra la **devolución**, que recoge la fecha de devolución y el estado en que volvió. Comparar el estado a la salida y a la vuelta es lo que hace visible un daño en tránsito en lugar de discutible.
:::
::::

## Paso 7: Anota el mantenimiento

Antes de que el reloj saliera a exposición, Priya lo llevó a revisar. Añade un @doc(copies.recordMaintenance, "registro de mantenimiento") de tipo **Servicing**: un título, el relojero que lo realizó, la fecha, el coste, el estado antes y después, y una **próxima fecha de revisión** cinco años más tarde para que la aplicación pueda avisar cuando se acerque la siguiente revisión. Como una revisión completa a un movimiento vintage es algo significativo, decide incluirla en la procedencia del ejemplar.

## Paso 8: Construye la narrativa de procedencia

Por último, la historia de propiedad. Priya conoce el pasado del reloj gracias al catálogo de la subasta, y lo registra como @doc(copies.traceProvenance, "eventos de procedencia"), del más antiguo al más reciente:

- Un evento de **Origin** para su fabricación, fechado en 1968.
- Una **Ownership transfer** a la familia del propietario original, con la precisión de fecha en **Approximate**, porque el catálogo solo dice "circa 1975".
- Un evento de **Exhibition** para la exposición de la sociedad que acaba de completar.
- Su propia **Acquisition**, fechada con exactitud, vinculada a la transacción de compra del paso 2.

Dos cosas que notar. La precisión de fecha existe porque la procedencia suele ser incierta: un evento puede fecharse con exactitud, por mes, por año, de forma aproximada, o dejarse sin fecha, y se muestra en consecuencia. Y los eventos de procedencia no llevan importes: un evento vinculado a una compra o venta enlaza con su transacción, para que el dinero se quede en un único lugar.

## Paso 9: Lee la historia completa

Abre la **línea de tiempo** del ejemplar. Todo lo que acabas de registrar (la compra, la valoración, el seguro, los documentos, el préstamo de ida y vuelta, el mantenimiento y los eventos de procedencia) se lee como una sola historia cronológica. La vista por defecto se ciñe a las entradas significativas, y la vista completa añade las rutinarias. @doc(copyHistory.readTimeline) explica esta vista en detalle.

Esta es la recompensa: una sola pantalla que responde cuánto costó el reloj, cuánto vale, quién lo ha tenido, qué se le ha hecho, y qué demuestra todo lo anterior.

## Errores habituales que evitar

- **Registrar el precio de compra como una valoración.** Es una transacción. Esta distinción es la columna vertebral de todo el modelo.
- **Editar registros antiguos en lugar de añadir otros nuevos.** Una tasación nueva es una valoración nueva, una póliza renovada es un registro de seguro nuevo. El historial solo funciona si se acumula.
- **Dejar documentos sin adjuntar.** Un recibo archivado en la transacción que prueba es una evidencia. Un archivo suelto adjunto al ejemplar es un escaneo que tendrás que volver a identificar más adelante.

## Por dónde seguir

- Cada tipo de registro usado aquí tiene su propia guía detallada en la @doc(copyHistory.index, "sección de historial de ejemplares").
- Descubre cómo estos registros alimentan las cifras en @doc(insights.collectionStatistics).
- ¿Compartes la colección con otras personas? @doc(tutorials.inviteHousehold, "Invita a tu familia o club").
