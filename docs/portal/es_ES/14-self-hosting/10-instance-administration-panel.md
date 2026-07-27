---
id: instanceAdmin.panel
title: El panel de administración de instancia
slug: el-panel-de-administracion-de-instancia
section: alojamiento-propio
---

# El panel de administración de instancia

El panel de administración de instancia en `/instance-admin` es donde un @doc(instanceAdmin.grantAccess, "administrador de instancia") ve todas las cuentas del servidor: cuántas hay, quién está en ellas, y el puñado de acciones destructivas que solo debería tener un operador. Esta página describe qué puede hacer el panel y, tan importante como eso, qué no puede hacer de forma deliberada.

Si gestionas una instancia personal con una sola cuenta, puede que nunca necesites este panel. Se gana su lugar en instancias compartidas, como un servidor de club o de familia con varias cuentas.

:::note
El panel solo aparece para los usuarios que tienen el indicador de administrador de instancia. Cualquier otra persona que visite `/instance-admin` recibe una página de no encontrado, no de acceso denegado, así que el panel nunca anuncia su existencia.
:::

## La vista general

El panel se abre con una vista general de toda la instancia:

- Recuentos de **cuentas**, **usuarios**, **colecciones** y **elementos** en todo el servidor.
- **Cuentas creadas este mes** y **usuarios activos este mes**, para que puedas ver si la instancia está creciendo o tranquila.
- Un gráfico de **altas por mes** de los últimos doce meses.

Estos números son de toda la instancia. No revelan el contenido del catálogo de nadie.

## Explorar cuentas

El área de **Cuentas** lista todas las cuentas de la instancia, 25 por página, con el número de miembros y de colecciones de cada una.

Puedes buscar cuentas **por la dirección de correo de un miembro** y filtrar por rol. No es posible buscar por nombre de cuenta o de persona, porque los nombres están cifrados en la base de datos y no se pueden buscar ahí. El correo es la referencia fiable.

Al abrir una cuenta se muestran sus miembros, ordenados primero los propietarios, luego los editores y por último los lectores, junto con los recuentos de colecciones y elementos de la cuenta y sus quince entradas más recientes del registro de actividad.

## Motivos de eliminación

A quien elimina su usuario se le pregunta antes por qué, y la sección **Motivos de eliminación** es donde llegan esas respuestas, la más reciente primero, 25 por página.

Cada entrada es una frase y una fecha, nada más. No está ligada a quien la escribió y permanece cuando esa persona ya no está: el usuario se va, la frase se queda. No hay nada que abrir ni ninguna acción que realizar, así que la página se limita a listarlas.

Los motivos están cifrados en reposo, como todo lo demás que la gente escribe en la aplicación, por lo que esta página no tiene buscador.

## Opciones del sitio

El área **Opciones del sitio** reúne los ajustes del sitio de marketing público, las páginas que un visitante ve antes de iniciar sesión. Ese sitio está desactivado por defecto en una instancia autoalojada (consulta @doc(selfHosting.configure)), así que si nunca lo activaste, nada de lo que hay aquí cambia lo que ven tus usuarios.

### El banner de anuncio

El banner es la barra negra que cruza la parte superior de cada página del sitio de marketing. Es el sitio para una frase corta: una versión que quieres que se note, una ventana de mantenimiento, un evento.

Solo hace falta la frase. Todo lo demás es opcional:

- **Mostrar el banner** lo enciende y lo apaga. Ponlo en **No** y no aparece ninguna barra, hayas rellenado lo que hayas rellenado.
- **Versión** es la pequeña píldora verde de la izquierda, como `v0.9`. Déjala vacía y la píldora desaparece.
- **Enlace** es la dirección a la que apunta el banner, y **Texto del enlace** es lo que pulsa el visitante. Deja el enlace vacío para un banner que solo dice algo.
- **Frase** es el anuncio en sí.

El sitio de marketing se sirve en varios idiomas, así que la frase y el texto del enlace se escriben un idioma cada vez, con una pestaña para cada uno. Un idioma que dejes vacío recurre al inglés, lo que significa que rellenar solo el inglés ya da un banner a todos los visitantes. El punto verde de una pestaña indica que ese idioma tiene su propia frase.

La vista previa encima del formulario muestra la barra tal como la verá un visitante, en el idioma de la pestaña en la que estás. Guardar limpia por ti las páginas de marketing en caché, así que el cambio se ve al instante.

### Limpiar la caché de respuestas

Las páginas de marketing cambian pocas veces, así que cada una se renderiza una vez y luego se sirve desde una caché durante siete días. Eso mantiene rápido el sitio público, pero también significa que una edición puede pasar una semana sin verse.

**Limpiar caché** descarta todas las páginas cacheadas de una vez. Recurre a ello después de cambiar algo que muestra el sitio público y que la aplicación no conoce, como una página de documentación que editaste en el servidor. Guardar el banner y moderar un testimonio ya limpian la caché por su cuenta.

Limpiar no pierde nada. Cada página se vuelve a renderizar la próxima vez que alguien la pide, y el único coste es que el primer visitante espera ese renderizado. Lo mismo se puede hacer desde la línea de comandos con `php artisan responsecache:clear`, descrito en @doc(selfHosting.cliCommands).

## Las acciones destructivas

Tres acciones del panel cambian o eliminan datos, y ninguna se puede deshacer:

- **Eliminar una cuenta**, que elimina la cuenta con todas sus colecciones, elementos, ejemplares, miembros y todo su historial.
- **Eliminar un usuario**, que quita a esa persona de su cuenta.
- **Cambiar el indicador de administrador de otro usuario**, que concede o revoca la administración de instancia a otra persona.

:::warning
Eliminar una cuenta o un usuario desde este panel es inmediato y permanente. Nada pasa por la papelera, y no hay forma de restaurarlo. Comprueba dos veces que tienes la cuenta o la persona correcta antes de confirmar.
:::

Dos salvaguardas protegen a la propia instancia: un administrador no puede revocar su propio indicador ni eliminar su propio usuario desde el panel. Se use como se use, la instancia conserva siempre al menos un administrador funcional.

## Lo que el panel no es

El panel es exclusivamente web por diseño. La API JSON está limitada a una única cuenta, y una superficie de toda la instancia no tiene cabida en ella, así que ninguna de estas funciones existe como endpoint de API.

Las áreas de **Soporte** y **Reseñas** visibles en el panel son marcadores de posición y todavía no están construidas. Consulta @doc(troubleshooting.featureStatus).

## Por dónde seguir

- Concede o revoca el indicador en sí en @doc(instanceAdmin.grantAccess).
- Entiende qué pueden hacer ya los propietarios de cuenta sin ti en @doc(collaboration.manageMembersAndRoles).
- Repasa las demás herramientas del operador en @doc(selfHosting.cliCommands).
