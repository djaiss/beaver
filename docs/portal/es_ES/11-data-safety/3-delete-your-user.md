---
id: users.deleteSelf
title: Elimina tu usuario
slug: elimina-tu-usuario
section: seguridad-y-mantenimiento-de-datos
---

# Elimina tu usuario

Eliminar tu usuario te elimina a ti, la persona, de KolleK. No es lo mismo que eliminar la cuenta: la cuenta es el espacio de trabajo compartido, y @doc(accounts.delete, "eliminarla") destruye todo para todos. Esta página trata sobre eliminarte solo a ti.

## Antes de decidir

Dos situaciones parecen "eliminar mi usuario" pero no lo son:

- **Quieres que todo desaparezca.** Si eres el propietario y quieres que se elimine todo el catálogo y el espacio de trabajo, eso es @doc(accounts.delete).
- **Quieres salir de una cuenta compartida.** Eliminar tu usuario te elimina a ti y deja la cuenta y su catálogo con el resto de los miembros.

Si eres el único propietario de la cuenta y quedan otros miembros, asciende primero a otra persona a propietario desde @doc(collaboration.manageMembersAndRoles, "la gestión de miembros"), para que la cuenta no se quede sin uno.

## Elimínate a ti mismo

::::steps
:::step title="Abre la configuración de tu perfil"
Ve a la configuración de tu perfil y busca la zona de peligro al final.
:::

:::step title="Explica por qué te vas"
Se requiere un motivo (unas pocas palabras bastan, al menos tres caracteres). Llega a quien gestiona la instancia y ayuda a mejorar KolleK.

::screenshot{label="Formulario de eliminación de usuario con el campo de motivo"}
:::

:::step title="Confirma"
Confirma la eliminación en el diálogo. Tu sesión se cierra de inmediato y tu acceso deja de funcionar.
:::
::::

:::warning
Eliminar tu usuario es permanente. Tu acceso desaparece y no se puede restaurar desde la aplicación. Tu dirección de correo electrónico vuelve a quedar libre, así que podrías registrar una cuenta completamente nueva más adelante, pero empezaría vacía.
:::

## Qué ocurre con tus rastros

El historial de actividad de la cuenta mantiene su integridad: las entradas que creaste registran tu nombre tal como era en ese momento, así que el registro de auditoría del trabajo compartido no desarrolla huecos cuando te vas.

## A dónde ir ahora

- ¿Prefieres una limpieza automática en su lugar? Ver @doc(users.inactiveDeletion).
- Eliminar a otra persona de una cuenta compartida se hace en @doc(collaboration.manageMembersAndRoles).
