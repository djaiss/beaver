---
id: tutorials.setupForHobby
title: "Tutorial: Configura tu cuenta para una afición concreta"
slug: configura-tu-cuenta-para-una-aficion-concreta
section: tutoriales
---

# Tutorial: Configura tu cuenta para una afición concreta

Añadir un elemento es fácil. Añadir doscientos solo es fácil si la cuenta está preparada primero. En este tutorial adaptarás KolleK a una afición concreta antes de introducir datos en masa: darás forma al tipo de colección y a sus campos personalizados, construirás un mapa de ubicaciones que refleje tu espacio real, y sembrarás un vocabulario de etiquetas, de modo que cada elemento que añadas después sea rápido y coherente.

Seguiremos a Noah, que está a punto de catalogar unos trescientos discos de vinilo. El mismo enfoque funciona para cualquier afición, así que sustituye por la tuya sobre la marcha.

Cuenta con que esto te llevará media hora, y con que te ahorrará muchas horas más adelante.

## Antes de empezar

- Termina @doc(tutorials.catalogueFirstCollection, "Cataloga tu primera colección de principio a fin") o al menos el @doc(gettingStarted.quickStart, "inicio rápido"), para que el ciclo básico te resulte familiar.
- Conoce los conceptos detrás de los @doc(collectionTypes.overview, "tipos de colección y campos personalizados"), las @doc(locations.overview, "ubicaciones") y las @doc(tags.overview, "etiquetas"). Repásalas por encima si no.
- Piensa un momento en qué quieres registrar realmente de cada elemento. Diez minutos con papel y lápiz valen más que rehacer campos después de cincuenta entradas.

## Paso 1: Da forma al tipo de colección

Noah empieza con el tipo predefinido **Vinyl Records** que vino con su cuenta. Ya registra My Rating, un grupo **Release info** (Artist, Album, Release Year) y un grupo **Pressing details** (Pressing/Edition, Speed, Color Vinyl).

Eso se acerca a lo que quiere, pero él compra muchas prensas japonesas y le importa el estado de las fundas. Así que ajusta el tipo.

::::steps
:::step title="Abre el tipo"
Ve a los ajustes de tipos de colección y selecciona **Vinyl Records**. El editor guarda a medida que trabajas, así que no hay ningún botón de guardar que buscar.

::screenshot{label="Editor de tipos de colección mostrando los campos de Vinyl Records"}
:::

:::step title="Añade los campos que realmente vas a usar"
Noah añade un campo de texto **Country of Pressing** al grupo Pressing details, y un campo **Sleeve Condition** de tipo selección con las opciones con las que califica. Los tipos de campo disponibles son texto, número, fecha, sí o no, selección y valoración (hasta cinco estrellas).
:::

:::step title="Agrupa y ordena los campos"
Crea un grupo nuevo si un conjunto de campos pertenece junto, y arrastra los campos al orden en que quieres que aparezcan en el formulario del elemento. Los grupos existen únicamente para mantener legibles los formularios largos.
:::
::::

Por qué esto importa: los campos personalizados que defines ahora aparecen en el formulario de cada elemento de cualquier colección que use este tipo. Decidirlos de antemano significa trescientos registros coherentes en lugar de trescientos improvisados.

:::note
Diseña los campos pensando en las preguntas que harás más adelante. "Qué discos son de vinilo de color" solo se puede responder si Color Vinyl es un campo. Un detalle enterrado en una descripción no se puede filtrar.
:::

## Paso 2: Construye tu mapa de ubicaciones

Noah guarda sus discos en dos sitios: una sala de audio con tres estanterías, y cajas en un trastero. Modela exactamente eso, porque una ubicación en KolleK solo es útil si coincide con un lugar al que puedas ir físicamente.

::::steps
:::step title="Crea los lugares de nivel superior"
En los @doc(locations.setup, "ajustes de ubicaciones"), crea **Music Room** 🛋️ y **Storage** 📦. Son las habitaciones.
:::

:::step title="Anida las subdivisiones reales"
Bajo Music Room, crea **Shelf A**, **Shelf B** y **Shelf C**. Bajo Storage, crea **Crate 1** y **Crate 2**. Las ubicaciones se anidan tan profundo como necesites, así que una caja dentro de un cajón dentro de una habitación no es ningún problema.
:::
::::

Por qué esto importa: cada ejemplar apunta a una ubicación, y los movimientos posteriores se registran como @doc(copies.move, "historial de ubicación"). Un buen mapa ahora significa que "dónde está ese disco" siempre tiene una respuesta exacta.

## Paso 3: Siembra tu vocabulario de etiquetas

Las etiquetas atraviesan colecciones y jerarquías, lo que las hace ideales para las categorías que no encajan en ningún otro sitio. Noah crea su conjunto inicial desde los @doc(tags.manageAccount, "ajustes de etiquetas"): **Signed**, **First Pressing**, **Japanese Pressing**, **To Sell** y **Needs Cleaning**.

Dos costumbres mantienen útiles las etiquetas:

- Mantenlas pocas y reutilizables. Una etiqueta usada una sola vez es un dato que debería haber sido un campo o una nota.
- Ponte de acuerdo en la ortografía antes de que se unan otras personas. "Signed" y "Autographed" como etiquetas separadas te perseguirán después.

Siempre puedes crear una etiqueta al vuelo mientras editas un elemento, así que esta lista solo necesita cubrir las que ya sabes que quieres.

## Paso 4: Importa un tipo en lugar de construirlo

Hay un atajo que vale la pena conocer. Un tipo de colección se puede @doc(collectionTypes.importExport, "exportar e importar como JSON"). Si un amigo ya ha construido un buen tipo de Vinilos, puede exportarlo, y tú puedes importarlo pegando el JSON, con lo que traes de un solo paso el nombre, el color, los grupos, los campos y las opciones de selección.

:::note
Importar un tipo trae solo la definición del tipo. No importa elementos ni sus datos. Actualmente no existe importación de elementos ni de colecciones completas, y el estado real de eso se recoge en la @doc(troubleshooting.featureStatus, "página de estado de funciones").
:::

Noah importa un tipo "45 RPM Singles" que le compartió un amigo del club, y aparece junto a sus propios tipos, listo para asignarse a una colección.

## Paso 5: Crea la colección y conecta todo

Ahora las piezas encajan.

::::steps
:::step title="Crea la colección"
Noah crea una colección llamada "Vinyl", elige el emoji 💿 y escribe una breve descripción.
:::

:::step title="Activa los tipos que necesita"
Activa tanto el tipo **Vinyl Records** como el tipo importado **45 RPM Singles**. Una colección puede usar varios tipos, y cada elemento elige el que le corresponde.
:::

:::step title="Define la moneda"
Define la moneda de la colección en la que realmente compra discos. Puede ser distinta de la moneda por defecto de la cuenta, y todo el dinero de los ejemplares de esta colección se mostrará en ella.
:::
::::

## El resultado

Añade un disco ahora y nota la diferencia: el formulario pregunta exactamente lo que hace falta, el desplegable de ubicaciones ofrece estanterías reales, y las etiquetas que necesitas ya existen. A partir de aquí, introducir datos en masa es un ritmo, no una serie de decisiones.

## Errores habituales que evitar

- **Diseñar de más los campos.** Diez campos que rellenas superan a veinticinco que te saltas. Puedes añadir campos más adelante; rellenarlos con retraso es la parte tediosa.
- **Ubicaciones que no coinciden con la realidad.** Si no existe físicamente una Shelf B, la ubicación "Shelf B" quedará desactualizada de inmediato.
- **Usar etiquetas para lo que un campo hace mejor.** Un grado, un año o una valoración pertenecen a un campo personalizado, donde pueden ser un valor real, no una etiqueta.

## Por dónde seguir

- Empieza a introducir elementos con @doc(items.addAndEdit).
- Sigue correctamente tu pieza más valiosa en @doc(tutorials.trackValuableItem, "Sigue toda la vida de un elemento valioso").
- ¿Trabajas con otras personas? @doc(tutorials.inviteHousehold, "Invita a tu familia o club").
