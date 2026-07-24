---
id: search.overview
title: Busca en toda tu cuenta
slug: busca-en-toda-tu-cuenta
section: funciones-principales
---

# Busca en toda tu cuenta

Emma colecciona cómics, vinilos y cartas coleccionables. Tiene tres colecciones, unos cientos de objetos y el doble de ejemplares. Alguien le pregunta si todavía conserva aquel número de Spider-Man con la portada negra. No debería tener que recordar en qué colección está.

La búsqueda responde a esa pregunta desde una sola caja. Escribe una palabra y KolleK mira de una vez en todo lo que contiene tu cuenta: objetos, colecciones, ejemplares, fotos, préstamos, ubicaciones, conjuntos, sagas, categorías, etiquetas y documentos.

Ábrela desde **Buscar**, arriba en la barra lateral, o pulsa **⌘K** (**Ctrl K** en Windows y Linux) desde cualquier pantalla.

## Qué se busca

La búsqueda no mira solo los nombres. También mira las palabras archivadas alrededor de un registro, y eso es lo que la hace útil en lugar de meramente literal.

Buscar `spider` encuentra:

- un objeto llamado **Amazing Spider-Man #300**
- el ejemplar **ASM-300-B**, porque su objeto se llama así
- una foto llamada `spider-man-300-front.jpg`
- un objeto al que nunca llamaste «Spider-Man», porque le pusiste la etiqueta **spider-man**
- un préstamo a un amigo, porque el ejemplar prestado es ese cómic

Así, una búsqueda que arranca de una etiqueta, una categoría o una ubicación te lleva igualmente a lo que de verdad buscabas.

:::note
Todo lo que está en la papelera queda fuera de la búsqueda. @doc(dataSafety.restoreFromTrash, "Restáuralo") y volverá a ser localizable.
:::

## Cómo funciona la coincidencia

Bastan cuatro reglas.

**Cada palabra tiene que coincidir.** Añadir una palabra estrecha el resultado en lugar de ampliarlo. `miles davis` solo encuentra los registros donde aparecen ambas palabras. Si recibes demasiado, añade una palabra.

**Una palabra coincide desde su principio.** Escribir `spi` encuentra **Spider-Man**. Nunca tienes que terminar una palabra, pero sí empezarla: buscar `man` no encontrará **Spider-Man**, porque `man` no es el principio de ninguna de sus palabras.

**Las mayúsculas y la puntuación se ignoran.** `asm-300`, `asm 300` y `ASM_300` se comportan igual, lo que importa cuando tus propios identificadores usan guiones, puntos o guiones bajos y ya no recuerdas cuál.

**Las letras sueltas se ignoran.** Una letra por sí sola es demasiado común para indexarla, así que se descarta de tu búsqueda. Si buscas una sola letra y nada más, no obtienes nada en lugar de obtenerlo todo.

## Leer los resultados

Los resultados se agrupan por lo que son, con los objetos primero. Cada fila muestra el nombre, una insignia que dice de qué tipo de registro se trata, la colección a la que pertenece cuando la tiene, y una línea de contexto: cuántos ejemplares tiene un objeto, dónde se guarda un ejemplar, a quién fue un préstamo.

A la derecha de cada fila, **Coincide con el nombre** significa que todas las palabras que escribiste aparecían en el nombre del propio registro. **Coincide con el texto** significa que al menos una palabra se encontró más allá, por ejemplo en una descripción o una etiqueta. Las coincidencias de nombre se ordenan primero, así que la respuesta más cercana suele ser la primera fila.

Las cápsulas encima de los resultados permiten limitarte a un solo tipo de registro. Cada una tiene su propia dirección, así que una búsqueda solo de objetos se puede guardar en favoritos o compartir con un compañero de la misma cuenta.

Se muestran como máximo 50 resultados. Cuando hay más, el recuento bajo la lista indica cuántos coincidieron en total, y añadir una palabra a tu búsqueda es la forma más rápida de llegar al que quieres.

## Quién puede buscar qué

La búsqueda está limitada a tu cuenta. Nunca ves nada de otra cuenta, y nadie de fuera ve la tuya.

Dentro de tu cuenta todos los roles pueden buscar, y cada resultado abre una pantalla que ese rol tiene permitido abrir. Los @doc(accounts.usersAndRoles, "lectores") son la excepción en un punto: las etiquetas se gestionan en una pantalla que solo tienen propietarios y editores, así que un lector no recibe resultados de etiqueta por separado. Aun así encuentra todo lo *etiquetado* como `spider-man`, porque los nombres de etiqueta cuentan para los objetos que las llevan.

## Si falta algo

La búsqueda lee un índice que se mantiene al día mientras trabajas, así que un objeto nuevo es localizable en cuanto lo guardas.

Dos casos pueden dejarlo brevemente atrás:

**Has renombrado algo que otros registros mencionan.** Renombrar una colección obliga a reindexar cada uno de sus objetos con el nombre nuevo. Eso ocurre en segundo plano: dale un momento.

**Acabas de actualizar una instancia autoalojada.** El índice arranca vacío en una instalación existente y hay que construirlo una vez. Ejecuta @doc(selfHosting.cliCommands, "el comando de reconstrucción") y todo pasa a ser localizable:

```bash
php artisan search:rebuild-index
```

Ese comando se puede volver a ejecutar en cualquier momento sin riesgo, así que también es el remedio si el índice se desvía alguna vez.

## Y ahora

- @doc(items.tagAndFind) cubre el etiquetado, que es lo que permite a la búsqueda encontrar cosas que no nombraste literalmente.
- @doc(organizing.categoriesSetsAndSeries) explica las categorías, conjuntos y sagas que también alimentan la búsqueda.
- @doc(photos.library) tiene su propia búsqueda, para cuando repasas fotos en lugar de toda la cuenta.
