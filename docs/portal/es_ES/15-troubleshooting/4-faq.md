---
id: troubleshooting.faq
title: Preguntas frecuentes
slug: preguntas-frecuentes
section: solucion-de-problemas
---

# Preguntas frecuentes

Respuestas breves a las preguntas que surgen una y otra vez. Cada una enlaza a la página que trata el tema en profundidad.

## ¿Cuál es la diferencia entre un elemento y un ejemplar?

Un elemento es el tipo de cosa, como "Amazing Spider-Man #1". Un ejemplar es una instancia física concreta que realmente posees. Si posees tres unidades del mismo cómic, eso es un elemento con tres ejemplares, cada uno con su propio estado, ubicación, valor e historial. Esta es la idea más importante de KolleK. Consulta @doc(items.itemsVsCopies).

## ¿Puedo pertenecer a más de una cuenta?

No. Un usuario pertenece a exactamente una cuenta, y una dirección de correo solo puede tener un usuario. Esto también significa que una invitación a la cuenta de otra persona no puede ser aceptada por un correo que ya tiene su propia cuenta. Consulta @doc(accounts.usersAndRoles).

## ¿KolleK es realmente gratuito?

Sí. No hay ninguna facturación dentro de la aplicación: sin planes, sin niveles, sin funcionalidades detrás de un muro de pago. Autoalojar la instancia es gratis, y todas las funcionalidades están incluidas sin importar cómo la ejecutes. Consulta @doc(kollek.hostingOptions).

## ¿Cómo saco mis datos?

Hoy, desde dentro de la aplicación, puedes exportar @doc(collectionTypes.importExport, "las definiciones de tipos de colección como JSON"). Todavía no existe la exportación de un elemento individual ni de una colección completa. La respuesta completa para quienes autoalojan la instancia es una copia de seguridad a nivel de instancia de la base de datos y los archivos subidos, tratada en @doc(selfHosting.backupAndRestore). El resumen honesto está en @doc(dataSafety.backupCollectionData).

## ¿Por qué no puedo eliminar o degradar al último propietario?

Una cuenta siempre debe conservar al menos un propietario, de lo contrario nadie podría administrarla, invitar miembros ni eliminarla. Asciende primero a otra persona a propietario. Consulta @doc(collaboration.manageMembersAndRoles).

## ¿Dónde está la función de búsqueda?

Buscar en todo desde el panel principal todavía no está disponible; el cuadro que ves ahí es un marcador de posición. Lo que funciona hoy: filtrar dentro de una colección que tienes abierta, y buscar en tu biblioteca de fotos. Consulta @doc(troubleshooting.featureStatus).

## ¿Los webhooks ya funcionan?

A medias. Puedes registrar destinos y cada uno recibe una clave secreta de firma, pero todavía ningún evento de la aplicación dispara un webhook. La maquinaria de entrega está lista; los eventos llegarán a medida que crezca el producto. Consulta @doc(webhooks.overview).

## ¿Mis datos están cifrados, y qué protege eso?

Los campos sensibles se cifran en reposo en la base de datos con la clave de tu instancia. Eso protege el contenido de la base de datos si roban solo la base de datos. No es cifrado de extremo a extremo: quien opera la instancia posee la clave y puede acceder a los datos. Consulta @doc(dataSafety.howProtected).

## ¿Puedo añadir mis propios estados?

Sí. Abre **Estados de los elementos** en la configuración de la cuenta para añadir, renombrar o eliminar estados, incluidos los predefinidos (Nuevo, Como nuevo, Usado, Desgastado, Dañado). Consulta @doc(conditions.manage).

## Se ha eliminado algo. ¿Puedo recuperarlo?

Si era una colección, un elemento, un ejemplar, una categoría o un set, fue a la papelera y se puede restaurar durante 30 días de forma predeterminada. Las fotos, los documentos y los registros de historial se eliminan de inmediato y no se pueden recuperar desde dentro de la aplicación. Consulta @doc(dataSafety.restoreFromTrash).

## ¿Sigues atascado?

- Problemas al iniciar sesión: @doc(troubleshooting.signIn).
- Correos que faltan: @doc(troubleshooting.emailDelivery).
- Qué está terminado y qué no: @doc(troubleshooting.featureStatus).
