---
id: collections.manage
title: Crea y gestiona colecciones
slug: crea-y-gestiona-colecciones
section: funciones-principales
---

# Crea y gestiona colecciones

Una @doc(collections.overview, "colección") es el contenedor donde vive todo lo demás, así que suele ser lo primero que creas. Esta página cubre cómo crear una, cada opción del formulario, cómo editarla más tarde y qué ocurre realmente cuando eliminas una.

## Quién puede hacer esto

Crear, editar y eliminar colecciones requiere el @doc(accounts.usersAndRoles, "rol") de **editor** o **propietario**. Los lectores pueden explorar colecciones pero no crearlas ni cambiarlas.

## Crea una colección

Noah está empezando un catálogo para sus vinilos. Esto es lo que hace.

::::steps
:::step title="Empieza una colección nueva"
Desde la pantalla de colecciones, elige **Nueva colección**.

::screenshot{label="Pantalla de colecciones, botón Nueva colección"}
:::

:::step title="Nómbrala y descríbela"
Dale un **nombre**, como "Discos de vinilo", y opcionalmente una **descripción** corta y un **emoji** para que destaque en las listas.
:::

:::step title="Elige sus tipos de colección"
Elige qué @doc(collectionTypes.overview, "tipos de colección") se aplican. Noah elige el tipo ya preparado **Discos de vinilo** para que sus elementos tengan campos como Artista, Álbum y Prensado. Puedes habilitar varios tipos, o ninguno, y cambiar esto más tarde.
:::

:::step title="Configura la moneda y la visibilidad"
Elige la **moneda** para los valores de esta colección, y su **visibilidad**. Si no estás seguro, deja los valores predeterminados. Privada es el punto de partida más seguro.

::screenshot{label="Formulario de colección, campos de moneda y visibilidad"}
:::

:::step title="Guarda"
Guarda la colección. Aparece en tu lista, vacía y lista para su primer elemento.
:::
::::

## Cada campo, explicado

- **Nombre.** Cómo aparece la colección en todas partes. Obligatorio.
- **Descripción.** Una frase sobre lo que contiene. Opcional, pero útil en cuanto tienes muchas colecciones.
- **Emoji.** Un marcador visual elegido de una paleta fija de doce (📦 📚 💿 🃏 🍷 🎮 🧸 🪙 🖼️ ⌚ 👟 📷). Opcional.
- **Tipos de colección.** Los tipos que habilitas deciden qué campos personalizados pueden registrar los elementos de esta colección. Puedes habilitar más de uno, por ejemplo Cómics y Libros en una sola colección "Lectura".
- **Moneda.** Todo importe de dinero en esta colección (valores, estadísticas) usa esta moneda. Hay dieciocho monedas disponibles. Puede ser distinta de la moneda predeterminada de tu cuenta, lo cual es útil si, por ejemplo, compras tu vino en euros pero todo lo demás en dólares.
- **Visibilidad.** Para quién está pensada la colección: **privada** (solo tú), **compartida** (todos en tu cuenta), o **pública** (cualquiera con el enlace, solo lectura). El ajuste se registra ya hoy y se aplicará en cuanto llegue la función de compartir. La página de conceptos @doc(sharing.overview, "visibilidad y compartición") explica el modelo y su estado actual, y @doc(collections.share) explica paso a paso cómo cambiarla.

## Edita una colección

Abre la colección y elige editarla. Aparece el mismo formulario con los mismos campos, y puedes cambiar cualquiera de ellos en cualquier momento. Renombrar una colección o cambiar su emoji no afecta a nada dentro de ella.

## Elimina una colección

Abre la colección, elige eliminarla y confirma.

:::warning
Eliminar una colección también envía cada elemento que contiene a la papelera. La colección y sus elementos permanecen en la papelera durante un tiempo limitado (30 días por defecto), y luego se eliminan de forma permanente.
:::

Mientras esté en la papelera todavía puedes cambiar de opinión. Consulta @doc(dataSafety.restoreFromTrash).

## A dónde ir después

- Pon algo dentro: @doc(items.addAndEdit).
- Elige el diseño que le convenga: @doc(collections.chooseView).
- Muéstrasela a alguien: @doc(collections.share).
