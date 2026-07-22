---
id: accounts.usersAndRoles
title: Cuentas, usuarios y roles
slug: cuentas-usuarios-y-roles
section: conceptos-basicos
---

# Cuentas, usuarios y roles

KolleK está construido alrededor de un único espacio de trabajo, la cuenta, y de las personas que lo comparten. Esta página explica el límite y el modelo de permisos en lenguaje claro, para que nada relacionado con el acceso te sorprenda nunca.

## La cuenta es el límite

Una **cuenta** es un espacio de trabajo privado. Cada colección, elemento, ejemplar, tipo, etiqueta y ubicación vive dentro de exactamente una cuenta. Nada se filtra entre cuentas, y nadie fuera de la tuya puede ver su contenido a menos que decidas deliberadamente @doc(sharing.overview, "compartir una colección").

Cuando Emma se registró, KolleK creó dos cosas a la vez: su usuario personal y una cuenta nueva de la que es propietaria. Si invita a su pareja, Sam, él se une a su cuenta y trabaja en el mismo catálogo.

## Una persona, una cuenta

Un **usuario** es una persona autenticada, vinculada a una dirección de correo electrónico, y un usuario pertenece a exactamente una cuenta.

:::note
El mismo correo electrónico no puede estar en dos cuentas. Alguien que ya tiene su propia cuenta no puede aceptar una invitación a la tuya. Si quiere unirse a ti, tendría que usar una dirección de correo distinta, o eliminar antes su propia cuenta.
:::

## Los tres roles

Cada miembro de una cuenta tiene un rol, elegido al invitarlo y modificable después por un propietario:

- Un **lector** puede explorar todo en la cuenta, pero no puede crear ni cambiar nada. Leo, el amigo de Emma, es lector: puede admirar el catálogo, no editarlo.
- Un **editor** puede crear y modificar el contenido del catálogo: colecciones, elementos, ejemplares, fotos y todos los registros de historial. Sam es editor.
- Un **propietario** puede hacer todo lo que un editor puede hacer, y además administrar la cuenta en sí: invitar y eliminar miembros, cambiar roles, gestionar la configuración de la cuenta y eliminar la cuenta. Emma es la propietaria.

La lectura está abierta a todos los miembros, incluidos los lectores. Escribir requiere el rol de editor o propietario. Administrar la cuenta requiere el rol de propietario. La página @doc(collaboration.rolesInPractice, "los roles en la práctica") lo relaciona con tareas concretas si quieres ver la tabla completa.

Una cuenta siempre debe conservar al menos un propietario. KolleK no permite que se degrade o elimine al último propietario, así que una cuenta nunca puede quedarse bloqueada.

## Un indicador que no es un rol

Si alguna vez oyes hablar de un **administrador de la instancia**, se trata de algo completamente distinto. Es un indicador a nivel de servidor para quien opera la propia instalación de KolleK. No otorga nada adicional dentro de la cuenta propia de esa persona, y no tiene relación con los roles de lector, editor o propietario. Se explica en @doc(instanceAdmin.panel, "el panel de administración de la instancia") para quienes operan el servicio.

## A dónde ir a continuación

- Suma a alguien con @doc(collaboration.invitePeople).
- Cambia lo que puede hacer un miembro en @doc(collaboration.manageMembersAndRoles).
- Continúa con los conceptos en @doc(collections.overview).
