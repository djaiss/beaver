---
id: tutorials.catalogueFirstCollection
title: "Tutorial: Cataloga tu primera colección de principio a fin"
slug: cataloga-tu-primera-coleccion-de-principio-a-fin
section: tutoriales
---

# Tutorial: Cataloga tu primera colección de principio a fin

En este tutorial llevarás una cuenta completamente nueva hasta una colección real y con contenido. Crearás una colección, verás los campos personalizados que registra, añadirás un elemento con una foto de portada, registrarás el ejemplar físico que posees, anotarás lo que pagaste por él, añadirás una primera valoración y leerás las estadísticas resultantes.

Seguiremos a Emma, que colecciona cómics. Catalogó un elemento rápidamente en el @doc(gettingStarted.quickStart, "inicio rápido de cinco minutos"). Esta vez lo hace bien, y al final su catálogo sabrá cuánto costó su cómic, cuánto vale y dónde vive.

Cuenta con que esto te llevará entre veinte y treinta minutos.

## Antes de empezar

- Necesitas una cuenta en la que puedas iniciar sesión. Si no tienes una, @doc(accounts.create, "crea tu cuenta") primero.
- Deberías conocer la diferencia entre un elemento y un ejemplar. Si no estás seguro, lee ahora @doc(items.itemsVsCopies, "Elementos y ejemplares"). El tutorial se apoya en esa idea constantemente.
- Ten a mano algo real que poseas para catalogar, idealmente con una foto y un recuerdo aproximado de lo que pagaste por ello.

## Paso 1: Crea la colección

Cada elemento vive dentro de una @doc(collections.overview, "colección"), así que por ahí empieza todo.

::::steps
:::step title="Empieza una colección nueva"
Desde tu panel principal, elige **Nueva colección**.

::screenshot{label="Panel principal con el botón Nueva colección"}
:::

:::step title="Ponle nombre y una cara"
Emma llama a la suya "My Comics", elige el emoji 📚 y escribe una descripción de una línea. El emoji y la descripción son opcionales, pero hacen que la colección sea fácil de identificar más adelante.
:::

:::step title="Elige el tipo de colección"
Activa el tipo predefinido **Comics** para esta colección. El tipo es lo que decide qué campos personalizados pueden registrar los elementos de esta colección.
:::

:::step title="Deja por ahora la visibilidad y la moneda tal cual"
Una colección nueva es **privada** por defecto, lo que significa que solo tú puedes verla, y usa la moneda por defecto de tu cuenta. Ambas cosas se pueden cambiar más adelante. Guarda la colección.
:::
::::

Por qué esto importa: las decisiones de este formulario determinan todo lo que viene después. El tipo controla los campos que rellenas para cada elemento, y la moneda controla cómo se muestra el dinero en los ejemplares de esta colección.

## Paso 2: Mira lo que registra el tipo Comics

Tu cuenta llegó con una docena de @doc(collectionTypes.overview, "tipos de colección") predefinidos. Antes de añadir elementos, vale la pena ver qué te va a pedir el tipo Comics, para que nada en el formulario del elemento te sorprenda.

Abre los ajustes de tipos de colección y selecciona **Comics**. Encontrarás:

- **My Rating**, un campo de valoración por estrellas (hasta cinco).
- Un grupo **Publishing info**: Issue # (un número), Publisher (a elegir entre Marvel, DC, Image, Dark Horse o Independent), Writer, Artist y Cover Date.
- Un grupo **Condition & grading**: Variant y Signed, ambos preguntas de sí o no.

No tienes que cambiar nada. Si quieres añadir o reordenar campos, la @doc(collectionTypes.setup, "guía de configuración de tipos") lo cubre. Para este tutorial, los valores por defecto son exactamente lo que necesita Emma.

## Paso 3: Añade el elemento con sus detalles y su foto

Ahora la parte satisfactoria. Abre tu nueva colección.

::::steps
:::step title="Crea el elemento"
Elige añadir un **Nuevo elemento** y dale un **nombre**. Emma escribe "Amazing Spider-Man #300".
:::

:::step title="Rellena los campos personalizados"
Como la colección usa el tipo Comics, el formulario ofrece los campos que acabas de revisar. Emma pone **Issue #** en 300, **Publisher** en Marvel, y responde **Signed** con no. Rellena lo que sepas y omite el resto. Los campos vacíos no son ningún problema.

::screenshot{label="Formulario del elemento mostrando los campos personalizados de Comics"}
:::

:::step title="Sube una foto de portada"
Añade una **foto** del elemento. Se aceptan archivos JPEG, PNG, WebP y GIF de hasta 10 MB. Si añades varias, marca la mejor como foto principal. Se convertirá en la portada que reconocerás en todas las listas.
:::
::::

Por qué esto importa: los detalles descriptivos como el número de ejemplar y la editorial pertenecen al elemento, porque son ciertos para cualquier copia de ese cómic que exista en el mundo. Nada de lo que has escrito hasta ahora dice nada sobre el ejemplar físico que tiene Emma en sus manos. Eso viene a continuación.

## Paso 4: Registra el ejemplar que posees

Un elemento sin un @doc(items.itemsVsCopies, "ejemplar") es solo una entrada de enciclopedia. El ejemplar es la cosa física que posees.

::::steps
:::step title="Añade un ejemplar al elemento"
En el elemento, añade un **ejemplar**.
:::

:::step title="Califícalo y ubícalo"
Define su **estado**. Emma elige **Used** de la lista predefinida (New, Like New, Used, Worn y Damaged vienen con cada cuenta). Luego define su **ubicación**. Emma guarda el suyo en **Storage**, una de las ubicaciones por defecto, aunque puedes @doc(locations.setup, "construir tu propio mapa de ubicaciones") cuando quieras.
:::

:::step title="Comprueba el estado de posesión"
Deja el estado como **Owned**. Los demás estados (Ordered, Loaned out, Sold, etc.) existen para ejemplares que no están en tu estantería en este momento. Guarda.
:::
::::

:::note
Si tienes dos copias del mismo cómic, no crees un segundo elemento. Añade un segundo ejemplar a este. Cada ejemplar lleva su propio estado, ubicación, dinero e historial.
:::

## Paso 5: Registra lo que pagaste

Aquí es donde KolleK va más allá de una simple lista. El dinero nunca vive en el elemento ni en una nota. Vive en una **transacción** del ejemplar, para que tus registros se mantengan precisos a medida que crecen. La explicación completa está en @doc(copies.recordPaymentsAndValue).

::::steps
:::step title="Abre el historial del ejemplar"
Abre la pestaña **Historial** del elemento. Muestra un ejemplar a la vez, y de momento solo tienes uno.
:::

:::step title="Añade una transacción de compra"
Añade una **transacción** de tipo **Purchase**. Emma introduce el importe que pagó, la tienda como contraparte, y la fecha en que lo compró. Los impuestos, comisiones, gastos de envío y una referencia están disponibles si los necesitas.

::screenshot{label="Formulario de nueva transacción con el tipo Purchase seleccionado"}
:::
::::

Por qué esto importa: la transacción de compra es lo que da al ejemplar su **precio pagado** y su **fecha de adquisición**. Las estadísticas que verás en el paso 7 usan esa fecha para representar cómo creció tu colección.

## Paso 6: Añade una primera valoración

Lo que pagaste y lo que vale son datos distintos, y KolleK los mantiene separados a propósito. El valor se registra como una **valoración**.

Sigue en el historial del ejemplar y añade una **valoración**. Emma elige el tipo **Own estimate**, introduce lo que cree que valdría hoy el cómic, y define la confianza como **Medium**. Cuando algún día lo tase un profesional, añadirá una valoración nueva en lugar de editar esta, y la estimación anterior quedará como historial.

:::note
Un precio de compra es una transacción, nunca una valoración. El valor estimado del ejemplar siempre proviene de su valoración más reciente, y su precio pagado proviene de su primera transacción de adquisición.
:::

## Paso 7: Mira lo que has creado

Abre la colección. Deberías ver:

- Tu elemento con su foto de portada, en la vista de cuadrícula.
- Un recuento de un elemento, y un valor total que coincide con tu valoración.

Ahora abre las **estadísticas** de la colección. Incluso con un solo elemento hay algo que leer: el valor estimado total, el valor por ubicación, y la adquisición cayendo en el mes en que compraste el cómic. La @doc(insights.collectionStatistics, "guía de estadísticas") explica de dónde sale cada número.

## Lo que has conseguido

Has ejercitado todo el ciclo básico de KolleK: una colección con un tipo, un elemento con detalles y una foto, un ejemplar con un estado y una ubicación, una transacción que guarda el dinero, y una valoración que guarda el valor. Todas las funciones del producto se construyen sobre los registros que acabas de crear.

## Errores habituales que evitar

- **Crear elementos duplicados en lugar de ejemplares.** Dos copias del mismo cómic son un elemento con dos ejemplares.
- **Registrar el precio de compra como una valoración.** El precio que pagaste es una transacción de tipo Purchase. Las valoraciones son para lo que vale ahora.
- **Poner detalles del ejemplar en el elemento.** El estado, la ubicación y el dinero siempre pertenecen a un ejemplar, porque un segundo ejemplar diferirá en los tres.

## Por dónde seguir

- Adapta la cuenta a tu afición real en @doc(tutorials.setupForHobby, "Configura tu cuenta para una afición concreta").
- Profundiza en una pieza especialmente valiosa en @doc(tutorials.trackValuableItem, "Sigue toda la vida de un elemento valioso").
- ¿Catalogas con familia o amigos? Consulta @doc(tutorials.inviteHousehold, "Invita a tu familia o club").
