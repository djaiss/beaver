---
id: dataSafety.howProtected
title: Cómo se protegen tus datos
slug: como-se-protegen-tus-datos
section: conceptos-basicos
---

# Cómo se protegen tus datos

Un catálogo registra lo que posees, lo que vale y dónde se guarda. Eso es sensible por naturaleza, y KolleK lo trata como tal. Esta página explica las protecciones en términos de usuario, y es honesta sobre dónde terminan.

## Cifrado en reposo

Los campos sensibles (nombres, detalles de elementos, valores, y mucho más) se cifran en la base de datos usando la clave de cifrado de la instancia. Alguien que obtuviera una copia del archivo de la base de datos sin la clave encontraría las columnas sensibles ilegibles.

Esto ocurre de forma automática. No hay nada que activar ni que configurar como usuario.

## Todo cambio queda registrado

KolleK mantiene un registro de auditoría de las acciones de los usuarios. Cuando Sam edita un elemento, el registro muestra quién lo hizo, qué cambió y cuándo, y alimenta tanto el feed de actividad de la cuenta como el propio registro de cada elemento. El nombre de quien actúa se captura en el momento, así que el historial sigue siendo legible incluso si el usuario de esa persona se elimina más adelante. Consulta @doc(activity.feedAndAuditTrail).

## El límite honesto

:::note
El cifrado en reposo protege el contenido almacenado en la base de datos. No es cifrado de extremo a extremo. La aplicación puede leer tus datos para mostrártelos, y quien opere la instancia posee la clave de cifrado.
:::

En la práctica, esto significa que tu confianza sigue al operador. Si @doc(selfHosting.index, "autoalojas la instancia"), ese operador eres tú, y guardas la clave en tu propio hardware. Si alguien aloja KolleK por ti, técnicamente esa persona posee la clave, exactamente igual que con cualquier aplicación web alojada.

Dos consecuencias que conviene conocer:

- **La clave es valiosa.** Si se pierde, nadie puede recuperar los datos cifrados. Quienes operan una instancia deberían leer @doc(selfHosting.applicationKeyAndEncryption).
- **Las copias de seguridad importan.** El cifrado protege frente a las miradas ajenas, no frente a la pérdida de datos. Quienes autoalojan la instancia deberían seguir @doc(selfHosting.backupAndRestore).

## Lo que tú controlas

Tú eliges qué sale de la cuenta. Hoy nada lo hace: ninguna colección es accesible desde fuera de tu cuenta. Cada colección lleva una @doc(sharing.overview, "configuración de visibilidad") que registra para quién está pensada, y cuando llegue la compartición, una colección que hayas marcado como pública se convertirá en la única superficie que un desconocido podrá llegar a ver.

## A dónde ir a continuación

- Consulta quién cambió qué en @doc(activity.feedAndAuditTrail).
- Refuerza tu propio inicio de sesión con @doc(security.index).
- ¿Operas tu propia instancia? Lee @doc(selfHosting.applicationKeyAndEncryption).
