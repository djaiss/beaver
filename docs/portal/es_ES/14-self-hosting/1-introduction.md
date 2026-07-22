---
id: selfHosting.index
title: Descripción general del alojamiento propio
slug: alojamiento-propio
section: alojamiento-propio
---

# Descripción general del alojamiento propio

Ejecutar tu propia instancia de KolleK es una forma de primera clase y totalmente compatible de usar el producto, y es gratuita. Esta página te cuenta a qué te comprometes antes de instalar nada, y te da la única regla que importa más que todas las demás.

Si todavía no has decidido entre alojar tu propia instancia o usar una instancia gestionada, empieza por @doc(kollek.hostingOptions).

## Qué implica gestionar una instancia

KolleK se distribuye como una única imagen Docker que cumple tres roles, seleccionados mediante una variable de entorno:

- El rol **web** sirve la aplicación en sí.
- El rol **queue** procesa trabajos en segundo plano (envío de correos, entregas de webhooks, registro de actividad).
- El rol **scheduler** ejecuta las tareas de mantenimiento diarias.

El archivo de Docker Compose incluido arranca los tres, más una base de datos MySQL. Las sesiones, la caché y la cola dependen todas de la base de datos, así que no hay Redis ni ningún otro servicio adicional que gestionar. Las fotos y documentos subidos viven en un volumen de almacenamiento, en disco local por defecto, con la opción de usar almacenamiento compatible con S3.

Los requisitos son modestos: una máquina con Docker Engine 24 o superior y el plugin de Compose. Un servidor virtual pequeño ejecuta sin problemas una instancia personal.

## La regla que debes interiorizar ahora

KolleK cifra los datos sensibles en reposo usando la clave de aplicación de tu instancia.

:::warning
Define la clave de aplicación una sola vez, antes del primer arranque, y nunca la cambies en una instancia ya en funcionamiento. Si la clave cambia, todos los campos cifrados y todas las sesiones quedan ilegibles de forma permanente. Trata la clave como si fueran los propios datos: haz una copia de seguridad y mantenla idéntica en todos los contenedores.
:::

Vale la pena leer esto con calma antes de instalar. @doc(selfHosting.applicationKeyAndEncryption) explica qué protege la clave, cómo guardarla y la única forma segura de rotarla de manera deliberada.

## Tus responsabilidades

Alojar tu propia instancia significa que tú eres el operador. En concreto, eso implica:

- **Instalación y actualizaciones.** Ambas son procedimientos de Docker breves y documentados.
- **Copias de seguridad.** No hay ninguna copia de seguridad automática dentro de la aplicación. Tú respaldas la base de datos y el volumen de almacenamiento, junto con la clave de aplicación.
- **Entrega de correo.** Una instancia recién instalada registra los correos en el log en lugar de enviarlos, así que las invitaciones y los enlaces de acceso no llegan a ningún sitio hasta que configuras un servidor de correo.
- **Mantener los tres roles en funcionamiento.** En particular, los trabajos en segundo plano y el mantenimiento diario se detienen en silencio si los contenedores de cola o de programador están caídos.

Alex, que gestiona una instancia para su club de coleccionistas, dedica unos minutos al mes a esto una vez terminada la configuración inicial. No es una carga operativa pesada, pero es suya.

## Esta sección

Recorre las páginas más o menos en este orden:

1. @doc(selfHosting.installDocker). De la nada a una instancia en funcionamiento.
2. @doc(selfHosting.configure). Las variables de entorno que realmente vas a tocar.
3. @doc(selfHosting.setupEmailDelivery). Haz que las invitaciones y los enlaces mágicos se envíen de verdad.
4. @doc(selfHosting.applicationKeyAndEncryption). La regla operativa más importante.
5. @doc(selfHosting.upgrade). Pasa a una nueva versión de forma segura.
6. @doc(selfHosting.backupAndRestore). Protege los datos.
7. @doc(selfHosting.scheduledJobs). Lo que la aplicación hace por su cuenta cada noche.
8. @doc(instanceAdmin.grantAccess). Inicializa el administrador de toda la instancia.
9. @doc(instanceAdmin.panel). Qué puede ver y hacer ese administrador.
10. @doc(selfHosting.cliCommands). Los comandos artisan que necesita un operador.
11. @doc(selfHosting.addLanguage). Cómo se traduce la interfaz.

## Por dónde seguir

- ¿Listo para instalar? Ve a @doc(selfHosting.installDocker).
- ¿Prefieres un recorrido guiado de principio a fin? Sigue el @doc(tutorials.selfHostWithDocker, "tutorial de alojamiento propio").
