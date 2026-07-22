---
id: instanceAdmin.grantAccess
title: Concede acceso de administrador de instancia
slug: concede-acceso-de-administrador-de-instancia
section: alojamiento-propio
---

# Concede acceso de administrador de instancia

Un administrador de instancia es la persona que cuida del servidor en sí, con un panel que abarca todas las cuentas de la instancia. Esta página explica qué es el indicador, cómo concederlo y las salvaguardas que lo rodean.

## Qué es el indicador, y qué no es

El indicador de administrador de instancia es de ámbito global y está completamente separado de los @doc(accounts.usersAndRoles, "roles de cuenta"). Concede exactamente una cosa: acceso al @doc(instanceAdmin.panel, "panel de administración de instancia").

- No otorga ningún poder adicional dentro de la propia cuenta del administrador. Un administrador de instancia que sea lector en su cuenta sigue sin poder editar elementos allí.
- Es por usuario, no por cuenta. Concédelo a la persona concreta que gestiona el servidor, normalmente tú mismo.

Alex, que gestiona la instancia del club, tiene el indicador en su propio usuario y es un propietario normal dentro de su propia cuenta. Son dos hechos independientes.

## Conceder y revocar

El indicador se gestiona desde la línea de comandos, algo deliberado: el acceso inicial al panel de toda la instancia debería requerir acceso al servidor.

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

Revócalo de la misma forma:

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com --revoke
```

Un administrador ya existente también puede activar o desactivar el indicador de otros usuarios desde dentro del panel.

## Por qué el panel finge no existir

Para cualquiera que no tenga el indicador, `/instance-admin` responde **404 Not Found**, no "acceso denegado". El panel no anuncia su existencia a quien no puede usarlo, así que sondear una instancia no revela nada. Si te concediste el indicador y sigues viendo un 404, comprueba que has iniciado sesión exactamente con el usuario al que se lo concediste.

## Las salvaguardas contra el bloqueo

Dos reglas protegen a la instancia de quedarse sin administrador:

- Un administrador no puede revocar su propio indicador desde el panel.
- Un administrador no puede eliminar su propio usuario desde el panel.

Así que el panel nunca se puede usar para bloquear a todo el mundo fuera del panel. E incluso si desaparecieran todos los administradores, la vía de la línea de comandos anterior siempre funciona, porque solo requiere acceso al servidor.

## Por dónde seguir

- Consulta qué puede hacer el panel en @doc(instanceAdmin.panel).
- Explora las demás herramientas del operador en @doc(selfHosting.cliCommands).
