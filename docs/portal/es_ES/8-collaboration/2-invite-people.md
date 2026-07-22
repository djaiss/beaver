---
id: collaboration.invitePeople
title: Invita a personas a tu cuenta
slug: invita-a-personas-a-tu-cuenta
section: colaboracion
---

# Invita a personas a tu cuenta

Catalogar es más divertido, y más preciso, cuando las personas que comparten la colección comparten también el catálogo. Esta página te muestra cómo invitar a alguien a tu cuenta, qué experimentará esa persona y los límites que conviene conocer antes de enviar la invitación.

Solo un **@doc(accounts.usersAndRoles, "propietario")** puede invitar a personas. Si no ves la opción, pídeselo a un propietario de tu cuenta.

## Decide primero el rol

Cada invitación lleva un @doc(collaboration.rolesInPractice, "rol"), elegido en el momento en que invitas:

- **Lector** puede explorar todo pero no cambiar nada. Es el valor predeterminado.
- **Editor** puede crear y cambiar contenido del catálogo.
- **Propietario** puede hacerlo todo, incluyendo gestionar miembros y la configuración de la cuenta.

Empieza a las personas en el rol más bajo que encaje. Siempre puedes @doc(collaboration.manageMembersAndRoles, "subirlo más adelante"), lo cual es más fácil que retirar un acceso que alguien no debería haber tenido.

Emma, por ejemplo, invita a su pareja Sam como **editor** para que Sam también pueda añadir cómics, y a su amigo Leo como **lector** para que Leo pueda explorar la colección sin poder cambiarla.

## Envía una invitación

::::steps
:::step title="Abre los miembros de tu cuenta"
Ve a la configuración de tu cuenta y abre el área de miembros. Verás los miembros actuales y cualquier invitación pendiente.

::screenshot{label="Pantalla de miembros con el formulario de invitación"}
:::

:::step title="Introduce el correo electrónico y elige un rol"
Escribe el **correo electrónico** de la persona y elige su **rol**. Si dejas el rol tal cual, se unirá como lector.
:::

:::step title="Envíala"
Envía el formulario. KolleK le envía a la persona un correo con un enlace para unirse a tu cuenta, y la invitación aparece en tu lista de pendientes.
:::
::::

Si invitas al mismo correo otra vez mientras una invitación anterior sigue pendiente y no ha caducado, KolleK reutiliza la invitación existente en lugar de acumular duplicados.

## Qué experimenta la persona invitada

La persona recibe un correo con un enlace. Al abrirlo, ve quién la invitó y a qué cuenta. Para unirse, rellena su **nombre**, **apellidos** y una **contraseña**. Se aplican las mismas comprobaciones de contraseña que en el registro: al menos ocho caracteres, y nada que haya aparecido en una filtración conocida.

Una vez que envía el formulario, es miembro de tu cuenta con el rol que elegiste, su correo ya está verificado, y su sesión queda iniciada. No hay nada más que tengas que hacer.

## Los límites que conviene conocer

:::note
Las invitaciones caducan a los siete días. Si alguien se pierde el plazo, simplemente invítalo de nuevo.
:::

Un límite merece especial atención, porque es la razón más habitual por la que una invitación falla:

- **Una persona pertenece exactamente a una cuenta.** Si el correo que invitas ya tiene su propia cuenta de KolleK, esa persona no puede aceptar tu invitación. Necesitaría usar una dirección de correo distinta, o @doc(users.deleteSelf, "eliminar su usuario existente") primero.
- **Solo los propietarios pueden invitar.** Los editores y lectores no pueden incorporar a personas nuevas.

Si un correo de invitación nunca llega, puede que la entrega de correo de la instancia todavía no esté configurada. Ver @doc(troubleshooting.emailDelivery, "solución de problemas de entrega de correo").

## A dónde ir ahora

- Ajusta el acceso o elimina a alguien en @doc(collaboration.manageMembersAndRoles).
- Comprueba exactamente qué permite cada rol en @doc(collaboration.rolesInPractice).
- Recorre una configuración completa en el tutorial @doc(tutorials.inviteHousehold, "Invita a tu hogar o club").
