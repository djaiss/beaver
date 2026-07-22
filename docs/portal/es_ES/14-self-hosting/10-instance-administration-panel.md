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
