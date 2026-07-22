---
id: selfHosting.addLanguage
title: Añade un idioma
slug: anade-un-idioma
section: alojamiento-propio
---

# Añade un idioma

KolleK se distribuye en siete idiomas: inglés, francés, español, alemán, portugués de Brasil, chino simplificado y japonés. Cada usuario elige su propio idioma desde su perfil, e incluso puede cambiarlo desde la página de inicio de sesión. Esta página explica cómo funcionan las traducciones por dentro, y cómo un operador o colaborador añade un idioma nuevo o completa uno existente.

Si solo quieres cambiar el idioma que ves, no necesitas nada de esto. Consulta @doc(profile.changeLanguage).

## Cómo se guardan las traducciones

Cada idioma es un archivo JSON en `lang/`, con el nombre del código de idioma, por ejemplo `lang/fr_FR.json`. Cada archivo asocia la cadena original en inglés con su traducción. La lista de idiomas que ofrece la aplicación se define en la configuración de la aplicación como los idiomas admitidos.

## Preparar o actualizar un idioma

El comando `kollek:localize` recorre toda la aplicación en busca de cadenas traducibles y las sincroniza con el archivo de un idioma:

```
php artisan kollek:localize fr_FR
```

Las cadenas nuevas desde la última ejecución se añaden, y las que ya no existen se eliminan. En el archivo de inglés, cada cadena es su propia traducción, así que el inglés siempre está completo por definición. En cualquier otro idioma, las cadenas nuevas llegan vacías, listas para que un traductor las rellene.

Añadir un idioma completamente nuevo sigue el mismo proceso: registra el idioma en la configuración de idiomas admitidos, ejecuta el comando con el nuevo código de idioma para generar su archivo, y traduce las entradas vacías.

:::note
Una traducción vacía recurre al inglés en lugar de romper la interfaz, así que un idioma parcialmente traducido se puede usar mientras el trabajo continúa.
:::

## Qué todavía no está traducido

La aplicación una vez iniciada la sesión es totalmente traducible. El sitio público de marketing y la referencia de API generada aún no están traducidos y siempre se muestran en inglés, sea cual sea el idioma del visitante. Consulta @doc(troubleshooting.featureStatus).

## Por dónde seguir

- Ejecuta el comando en tu instancia con @doc(selfHosting.cliCommands).
- Consulta el lado del lector de todo esto en @doc(profile.changeLanguage).
