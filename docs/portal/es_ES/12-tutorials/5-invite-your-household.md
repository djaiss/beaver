---
id: tutorials.inviteHousehold
title: "Tutorial: Invita a tu familia o club y define los permisos"
slug: invita-a-tu-familia-o-club-y-define-los-permisos
section: tutoriales
---

# Tutorial: Invita a tu familia o club y define los permisos

Una cuenta de KolleK es un espacio de trabajo compartido, y traer gente de forma segura es sobre todo cuestión de elegir el rol adecuado para cada persona. En este tutorial invitarás a dos personas con roles distintos, verás qué puede y qué no puede hacer cada una, compartirás una colección públicamente mientras mantienes otra privada, y ajustarás un rol después de asignarlo.

Seguiremos a Emma, que cataloga cómics con su pareja Sam y le gusta enseñar su colección a su amigo Leo. Sam ayuda con la introducción de datos, así que necesita poder editar. Leo solo mira, así que no debería poder cambiar nada.

Cuenta con que esto te llevará unos quince minutos, más lo que tarden tus invitados en abrir su correo.

## Antes de empezar

- Debes ser **propietario** de la cuenta. Solo los propietarios pueden invitar a personas y cambiar roles.
- Lee @doc(accounts.usersAndRoles) si no lo has hecho. La versión resumida: los lectores leen, los editores cambian el contenido del catálogo, los propietarios además gestionan la cuenta.
- Ten a mano los correos de tus invitados, y un detalle sobre ellos: una invitación solo funciona para un correo que todavía no tenga su propia cuenta de KolleK, porque una persona pertenece exactamente a una cuenta.

## Paso 1: Invita a Sam como editor

::::steps
:::step title="Abre el área de miembros"
Ve a los ajustes de miembros de tu cuenta, donde se listan los miembros y las invitaciones pendientes.

::screenshot{label="Pantalla de miembros con el formulario de invitación"}
:::

:::step title="Envía la invitación"
Introduce el **correo** de Sam, elige el rol **Editor**, y envía. Sam ya puede crear y editar colecciones, elementos y ejemplares, pero no puede invitar a personas ni tocar los ajustes de la cuenta.
:::
::::

El correo de invitación contiene un enlace válido durante **siete días**. Si caduca antes de que Sam lo abra, simplemente vuelve a invitarlo.

## Paso 2: Invita a Leo como lector

Repite los mismos pasos con Leo, pero deja el rol como **Viewer**, que es el valor por defecto. Leo podrá explorar todo en la cuenta, incluidas las colecciones, los elementos y sus historiales, pero cualquier control de edición quedará fuera de su alcance.

Elegir el rol más pequeño no es una descortesía. También protege a Leo: no puede eliminar un elemento por accidente ni cambiar un registro mientras explora.

## Paso 3: Qué experimentan Sam y Leo

Cada uno recibe un correo y abre el enlace. Como ninguno de los dos tiene todavía una cuenta de KolleK, la página les pide su **nombre**, sus **apellidos** y una **contraseña** (de al menos ocho caracteres, comprobada contra filtraciones conocidas). Luego llegan a la cuenta de Emma, ya verificados y con la sesión iniciada, con el rol que ella eligió.

Si el enlace indica que ya existe una cuenta para ese correo, esa persona no puede unirse a través de esta invitación. Esa situación y otros contratiempos con las invitaciones se cubren en @doc(troubleshooting.signIn).

## Paso 4: Define la visibilidad de cada colección

Los roles controlan a las personas dentro de la cuenta. La @doc(sharing.overview, "visibilidad") registra para quién está pensada cada colección, desde solo tú hasta cualquiera que tenga un enlace.

Emma tiene dos colecciones: "My Comics", que algún día quiere mostrar al mundo, y "Wishlist Research", que no es asunto de nadie más que suyo.

::::steps
:::step title="Marca una colección como pública"
En "My Comics", define la visibilidad como **Public**, marcándola como la que tiene intención de compartir más allá de la cuenta.
:::

:::step title="Marca la otra como privada"
"Wishlist Research" se define como **Private**, pensada solo para ella. **Shared**, el ajuste intermedio, marca una colección como pensada para todos los miembros de la cuenta.
:::
::::

:::note
La visibilidad todavía no se aplica. Hoy en día, Sam y Leo pueden seguir explorando cualquier colección de la cuenta, incluidas las privadas, y no existe ningún enlace público que compartir, así que de momento nada es visible fuera de la cuenta. Definir la visibilidad ahora hace que cada colección se comporte correctamente en el momento en que llegue el uso compartido. Consulta @doc(troubleshooting.featureStatus).
:::

:::warning
Cuando lleguen los enlaces públicos, una colección pública podrá verla cualquiera que tenga el enlace, sin iniciar sesión. Marca una colección como pública solo si te resulta cómodo que se vea cada elemento que contiene.
:::

El recorrido completo, incluyendo cómo revertirlo, está en @doc(collections.share).

## Paso 5: Ajusta un rol más adelante

Unas semanas después, Leo empieza a notar errores y quiere corregirlos él mismo. Emma abre la pantalla de miembros, encuentra a Leo, y cambia su rol de **Viewer** a **Editor**. El cambio se aplica de inmediato. Los roles son un dial, no una condena de por vida, y bajar de rol a alguien funciona igual.

Una salvaguarda que conviene conocer: una cuenta siempre debe conservar al menos un **propietario**. KolleK se negará a degradar o eliminar al último propietario, así que una cuenta compartida nunca puede quedarse sin propietarios y sin nadie que la gestione.

:::warning
Eliminar a un miembro borra su usuario de la cuenta y no se puede deshacer desde la pantalla de miembros. Si alguien solo necesita menos acceso, cambia su rol en lugar de eliminarlo.
:::

## El resultado

La cuenta de Emma ahora tiene tres personas con tres niveles de confianza: Emma es propietaria y gestiona, Sam cataloga junto a ella, y Leo explora y, últimamente, corrige. Una colección está marcada para el mundo, otra solo para ella, lista para el día en que el uso compartido se aplique de verdad. Nada de esta configuración es fijo; los roles y la visibilidad pueden cambiar según cambien las personas.

## Errores habituales que evitar

- **Invitar a todo el mundo como editor por defecto.** Da a cada persona el rol que necesita hoy. Subir de rol después es un clic.
- **Suponer que privada ya oculta una colección.** La visibilidad se registra pero todavía no se aplica, así que hoy cualquier miembro puede explorar cualquier colección, privada o no. Mantén los catálogos verdaderamente personales en una cuenta propia por ahora.
- **Eliminar a un miembro para reducir su acceso.** Eliminar es destructivo. Cambiar el rol no lo es.

## Por dónde seguir

- La referencia completa de quién puede hacer qué está en @doc(collaboration.rolesInPractice, "Entender los tres roles en la práctica").
- Gestiona la cuenta en sí, nombre, moneda y más, en @doc(accounts.settings).
- ¿Gestionas tú mismo la instancia de tu club? Consulta @doc(tutorials.selfHostWithDocker, "Aloja tu propia instancia de KolleK con Docker").
