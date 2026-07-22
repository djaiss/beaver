---
id: collectionTypes.setup
title: Configura tipos de colección y campos personalizados
slug: configura-tipos-de-coleccion-y-campos-personalizados
section: organizacion
---

# Configura tipos de colección y campos personalizados

Un @doc(collectionTypes.overview, "tipo de colección") decide qué detalles puede registrar un elemento. Un cómic quiere un número de edición y una editorial. Un disco de vinilo quiere un artista y una edición de prensado. Esta página te muestra cómo crear un tipo, añadirle campos personalizados y mantener los formularios largos legibles con grupos de campos.

Necesitas el rol de editor o propietario para gestionar tipos. Los tipos son de toda la cuenta, así que un tipo que configures con cuidado una vez puede reutilizarse en cualquier número de colecciones.

## Empieza por los tipos ya preparados

Una cuenta recién creada ya incluye una docena de tipos preparados (Cómics, Cartas coleccionables, Discos de vinilo, CD, DVD, Monedas, Sellos, Libros, Figuras de acción / Juguetes, Videojuegos, Relojes y Vino), cada uno con campos sensatos ya agrupados. Antes de construir uno desde cero, abre el que más se parezca a tu afición y ajústalo. Cambiar el nombre de un campo o añadir uno es más rápido que empezar de cero.

## Crea un tipo

Noah colecciona vinilos y quiere un tipo para carteles de conciertos, algo que los tipos predeterminados no cubren.

::::steps
:::step title="Abre los tipos de colección"
Ve a los ajustes de tu cuenta y abre **Tipos de colección**.

::screenshot{label="Lista de tipos de colección en los ajustes de la cuenta"}
:::

:::step title="Crea el tipo"
Elige **Nuevo tipo**, dale un nombre (Noah escribe "Carteles de conciertos") y elige un color. El color te ayuda a distinguir los tipos de un vistazo en las listas.
:::

:::step title="Añade tus primeros campos"
Abre el tipo nuevo y añade un campo personalizado por cada detalle que quieras registrar. Para cada campo, elige un nombre y un tipo de campo.

::screenshot{label="Editor de tipos con la lista de campos"}
:::
::::

El editor de tipos guarda a medida que avanzas. No hay un botón de guardar aparte que recordar; cada cambio se almacena en el momento en que lo haces.

## Elige el tipo de campo adecuado

Cada campo personalizado tiene uno de seis tipos de campo:

- **Texto** para detalles de forma libre, como un artista o un local.
- **Número** para cantidades y medidas, como un número de edición o una tirada.
- **Fecha** para cualquier cosa basada en el calendario, como la fecha de un concierto.
- **Sí / No** para marcadores sencillos, como "Firmado" o "Primera edición".
- **Selección** para una lista fija de opciones que tú defines, como una editorial o una calificación. Las opciones mantienen los datos coherentes, porque todos eligen de la misma lista en lugar de escribir variaciones.
- **Valoración** para una puntuación personal de una a cinco estrellas.

Prefiere **Selección** frente a **Texto** siempre que los valores posibles sean una lista conocida. "Marvel" y "marvel comics" te parecen lo mismo a ti, pero no a un filtro.

## Mantén los formularios legibles con grupos de campos

Los campos se pueden organizar en grupos con nombre, y cada grupo se muestra como su propia sección en el formulario del elemento. El tipo Cómics ya preparado, por ejemplo, agrupa sus campos en "Información de publicación" y "Estado y calificación". Los campos sin agrupar aparecen primero.

Crea un grupo, dale un nombre y mueve campos a él. Puedes reordenar tanto los campos dentro de un grupo como los propios grupos, de modo que el formulario se lea en el orden que tenga sentido para tu afición.

:::note
Los grupos solo afectan a cómo se presenta el formulario del elemento. No cambian nada de los datos en sí, así que siéntete libre de reorganizarlos en cualquier momento.
:::

## Vincula el tipo a colecciones

Un tipo no hace nada hasta que una @doc(collections.overview, "colección") lo habilita. Al crear o editar una colección, elige qué tipos se aplican. Una colección puede habilitar varios, y el mismo tipo puede servir a muchas colecciones. Una vez habilitado, los elementos de esa colección pueden elegir el tipo y rellenar sus campos.

## A dónde ir después

- Comparte una configuración de la que estés orgulloso, o toma prestada una, con @doc(collectionTypes.importExport).
- Pon los campos a trabajar en @doc(items.addAndEdit).
- Completa tu configuración con @doc(locations.setup).
