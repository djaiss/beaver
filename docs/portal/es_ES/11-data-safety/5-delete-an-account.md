---
id: accounts.delete
title: Elimina una cuenta
slug: elimina-una-cuenta
section: seguridad-y-mantenimiento-de-datos
---

# Elimina una cuenta

Eliminar una cuenta es la acción más destructiva de KolleK. Elimina todo el espacio de trabajo: cada colección, cada elemento, cada ejemplar con todo su historial, cada foto y documento, y el acceso de todos los miembros. Solo un @doc(accounts.usersAndRoles, "propietario") puede hacerlo.

:::warning
Eliminar una cuenta no se puede deshacer. Nada va a la papelera, nada se puede restaurar, y nadie, ni siquiera quien gestiona la instancia, puede recuperarla. Cada miembro lo pierde todo de una vez.
:::

## Antes de eliminar

Tómate tu tiempo y comprueba tres cosas:

- **¿Es esto realmente lo que quieres, en lugar de @doc(users.deleteSelf, "eliminar tu propio usuario")?** Salir de una cuenta compartida solo requiere eliminarte a ti mismo. La cuenta y el catálogo sobreviven sin ti.
- **¿Depende alguien más de ella?** Cada miembro de la cuenta pierde el acceso y sus datos en el momento en que confirmas. Avísales primero.
- **¿Tienes lo que necesitas fuera de ella?** Exporta cualquier @doc(collectionTypes.importExport, "definición de tipo de colección") que quieras conservar. Si la instancia es autoalojada, haz primero una copia de seguridad completa, como se describe en @doc(selfHosting.backupAndRestore). Después de la eliminación no queda nada que respaldar.

## Elimina la cuenta

Desde la **Configuración de la cuenta**, busca la opción de eliminación en la zona de peligro, y confirma. La cuenta y todo lo que contiene se elimina, y todos los miembros cierran sesión de forma definitiva.

## Qué desaparece después

Todo. Colecciones, elementos, ejemplares, categorías, sets, series, etiquetas, ubicaciones, tipos y campos personalizados, fotos, documentos, los historiales completos de los ejemplares, el registro de actividad, todos los miembros, y cualquier invitación pendiente. Las direcciones de correo implicadas quedan libres para registrar cuentas nuevas, pero esas cuentas empiezan vacías.

## A dónde ir ahora

- Eliminarte solo a ti mismo se explica en @doc(users.deleteSelf).
- Para eliminaciones recuperables, ver @doc(dataSafety.restoreFromTrash).
