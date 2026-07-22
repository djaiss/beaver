---
id: collaboration.manageMembersAndRoles
title: Gestiona miembros y roles
slug: gestiona-miembros-y-roles
section: colaboracion
---

# Gestiona miembros y roles

La implicación de las personas cambia con el tiempo. Un lector empieza a ayudar con la entrada de datos y necesita convertirse en editor. Alguien deja el club y debería perder el acceso. Esta página trata cómo cambiar el rol de un miembro y cómo eliminar a un miembro, y la única salvaguarda que protege tu cuenta de quedarse sin acceso a sí misma.

Necesitas ser **@doc(accounts.usersAndRoles, "propietario")** para todo lo de esta página. La lista de miembros y las invitaciones pendientes solo son visibles para los propietarios.

## Ve quién está en tu cuenta

Abre el área de miembros desde la configuración de tu cuenta. Verás:

- **Miembros**, cada uno con su nombre, correo y rol actual.
- **Invitaciones pendientes** que se han enviado pero aún no se han aceptado, para que sepas quién está por llegar. Las invitaciones caducan a los siete días.

## Cambia el rol de un miembro

::::steps
:::step title="Encuentra al miembro"
En la lista de miembros, localiza a la persona cuyo acceso quieres cambiar.
:::

:::step title="Elige el nuevo rol"
Cambia su **rol** a lector, editor o propietario. El cambio surte efecto de inmediato, no hay correo de confirmación ni paso de aceptación.

::screenshot{label="Fila del miembro con el selector de rol abierto"}
:::
::::

Cuando el rol de Sam pasa de lector a editor, Sam puede empezar a añadir y editar elementos en el momento en que se guarda el cambio.

:::note
Una cuenta siempre debe mantener al menos un propietario. KolleK se negará a degradar al último propietario, así que no puedes dejar accidentalmente la cuenta sin nadie que pueda gestionarla. Asciende primero a otra persona a propietario si quieres dejar de serlo.
:::

## Elimina a un miembro

Eliminar a un miembro le quita el acceso por completo.

:::warning
Eliminar a un miembro elimina su usuario. Pierde el acceso de inmediato, y esto no se puede deshacer desde esta pantalla. Si más adelante debería volver, tendrás que invitarlo de nuevo y empezará desde cero.
:::

Sus contribuciones pasadas no desaparecen, sin embargo. El @doc(activity.feedAndAuditTrail, "registro de actividad") conserva el rastro de lo que hizo, porque cada entrada guarda el nombre de la persona en el momento en que se escribió.

Aquí se aplica la misma salvaguarda que con los roles: el último propietario no se puede eliminar.

## A dónde ir ahora

- Compara qué permite cada rol en @doc(collaboration.rolesInPractice).
- Incorpora a alguien nuevo con @doc(collaboration.invitePeople).
- Si en cambio estás cerrando toda la cuenta, lee @doc(accounts.delete).
