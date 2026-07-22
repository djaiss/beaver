---
id: selfHosting.scheduledJobs
title: Tareas de mantenimiento programadas
slug: tareas-de-mantenimiento-programadas
section: alojamiento-propio
---

# Tareas de mantenimiento programadas

Cada noche, tu instancia se ordena a sí misma. Esta página te explica qué se ejecuta, cuándo, y qué condiciones deben cumplirse para que ocurra, de modo que nada de lo que la aplicación hace por su cuenta te sorprenda nunca.

## Las tareas nocturnas

Tres tareas se ejecutan a diario, cada una en la cola de baja prioridad:

- **00:30, eliminación de usuarios inactivos.** Elimina a los usuarios que se han apuntado personalmente a la @doc(users.inactiveDeletion, "eliminación automática por inactividad") y llevan seis meses o más inactivos. Cada eliminación se notifica a la dirección definida en `ACCOUNT_DELETION_NOTIFICATION_EMAIL`. Los usuarios que nunca se apuntaron no se ven afectados.
- **01:00, limpieza de la papelera.** Elimina de forma permanente todo lo que hay en la @doc(dataSafety.restoreFromTrash, "papelera") más allá del periodo de retención (`TRASH_RETENTION_DAYS`, 30 días por defecto). Dentro de ese plazo, los objetos en la papelera se pueden restaurar.
- **02:00, marcado de préstamos vencidos.** Marca como vencidos los @doc(loans.lendAndBorrow, "préstamos") activos cuya fecha de devolución ya ha pasado, para que los coleccionistas vean de un vistazo qué no ha vuelto.

Las tres son seguras y esperables. Solo actúan sobre cosas que los usuarios han eliminado, elegido o fechado de forma explícita.

## Qué debe estar en funcionamiento

Dos contenedores hacen posible esto:

- El rol **scheduler** decide que ha llegado el momento y encola cada tarea.
- El rol **queue** las ejecuta de verdad.

:::note
Si alguno de los dos contenedores está caído, el mantenimiento se detiene en silencio: la papelera se acumula más allá de su plazo de retención, los préstamos vencidos siguen marcados como activos y los usuarios inactivos que se apuntaron no se limpian. Nada se rompe, pero nada se ejecuta. Comprueba `docker compose ps` si el comportamiento nocturno parece haberse detenido.
:::

Todo se pone al día en la siguiente ejecución exitosa; una noche perdida no es un problema.

## Por dónde seguir

- Ajusta el periodo de retención en @doc(selfHosting.configure).
- Consulta qué experimentan los usuarios al otro lado en @doc(dataSafety.restoreFromTrash).
