---
id: dataSafety.restoreFromTrash
title: Restaura algo desde la papelera
slug: restaura-algo-desde-la-papelera
section: seguridad-y-mantenimiento-de-datos
---

# Restaura algo desde la papelera

La mayoría de las eliminaciones cotidianas en KolleK no son definitivas. Las colecciones, elementos, ejemplares, categorías y sets van primero a la papelera, donde esperan antes de eliminarse para siempre. Esta página explica qué llega ahí, cuánto tiempo permanece y cómo recuperar algo.

Necesitas el rol de editor o propietario para restaurar o eliminar de forma permanente.

## Qué va a la papelera, y qué no

Cinco tipos de objetos se eliminan de forma reversible a la papelera:

- @doc(collections.manage, "Colecciones"), junto con lo que contienen
- @doc(items.addAndEdit, "Elementos")
- @doc(copies.track, "Ejemplares")
- @doc(categories.organizeItems, "Categorías")
- @doc(sets.trackCompletion, "Sets")

:::note
Las fotos, documentos y los registros de historial de un ejemplar (transacciones, valoraciones, préstamos y el resto) no van a la papelera. Eliminar uno de esos elementos lo quita de inmediato y de forma permanente.
:::

## Cuánto tiempo se guardan las cosas

Los objetos en la papelera se conservan durante un período de retención, 30 días a menos que quien gestiona tu instancia haya configurado uno distinto. Una limpieza diaria elimina de forma permanente todo lo que haya superado su plazo. Cada entrada en la papelera muestra cuántos días le quedan, y la lista se ordena con lo más urgente primero, así que lo que está a punto de desaparecer queda arriba.

## Restaura algo

::::steps
:::step title="Abre la papelera"
Ve a la **Papelera** desde tu cuenta. Puedes buscar en ella si la lista es larga.

::screenshot{label="Lista de la papelera con los días restantes por entrada"}
:::

:::step title="Encuentra la entrada"
Cada entrada muestra qué es, cuándo se eliminó y quién la eliminó.
:::

:::step title="Restáurala"
Elige **Restaurar**. El objeto vuelve exactamente a donde estaba, con sus datos intactos.
:::
::::

Si eliminaste una colección por error, restaurarla también recupera lo que contenía. Restaura primero los elementos padre antes de buscar sus elementos hijos.

## Vacía la papelera

También puedes eliminar de forma permanente todo lo que hay en la papelera de una vez, sin esperar a que termine el período de retención.

:::warning
Vaciar la papelera es permanente. Todo lo que contiene se elimina para siempre, y nada se puede recuperar después.
:::

## A dónde ir ahora

- ¿Quieres eliminarte a ti mismo en lugar de tus datos? Ver @doc(users.deleteSelf).
- ¿Te autoalojas y quieres redes de seguridad reales? Ver @doc(selfHosting.backupAndRestore).
