---
id: security.recoveryCodes
title: Guarda y usa tus códigos de recuperación
slug: codigos-de-recuperacion
section: seguridad
---

# Guarda y usa tus códigos de recuperación

Los códigos de recuperación son tu forma de volver a entrar si pierdes tu autenticador. Cuando activas la @doc(security.twoFactorAuth, "autenticación de dos factores"), KolleK genera ocho. Cada código funciona exactamente una vez, en lugar de un código de tu aplicación.

Los teléfonos se pierden, se rompen y se sustituyen. Los códigos de recuperación son lo que se interpone entre ese mal día normal y quedarte bloqueado fuera de tu catálogo.

## Dónde los consigues

Los códigos se muestran justo después de confirmar la configuración de dos factores. Ese es el momento en que deberías guardarlos.

Buenos lugares para conservarlos:

- Un gestor de contraseñas, en las notas de tu entrada de KolleK.
- Una hoja impresa en un cajón de casa.
- Un archivo cifrado del que hagas copia de seguridad.

Un mal lugar para conservarlos es solo tu teléfono, porque la situación en la que los necesitas es precisamente la situación en que tu teléfono ha desaparecido.

:::warning
Si pierdes tanto tu autenticador como tus códigos de recuperación, no puedes completar el paso de dos factores y puedes quedarte bloqueado fuera de tu usuario. No hay forma de solucionarlo por tu cuenta, así que guarda los códigos en un lugar seguro ahora.
:::

## Usa un código para iniciar sesión

Cuando KolleK pide tu código de autenticador de seis dígitos y no puedes proporcionar uno:

1. En el desafío de dos factores, introduce uno de tus códigos de recuperación en lugar del código de la aplicación.
2. Inicias sesión con normalidad.

Eso es todo. El desafío acepta tanto un código de autenticador actual como un código de recuperación sin usar.

## Cada código funciona una vez

Un código de recuperación se consume en el momento en que lo usas. Nunca volverá a funcionar, y tus códigos restantes siguen siendo válidos. Tacha los códigos usados donde los tengas guardados.

:::note
Si te estás quedando sin códigos, o sospechas que alguien más los ha visto, desactiva la autenticación de dos factores y vuelve a activarla. Al volver a activarla se genera un conjunto nuevo de ocho códigos y se invalidan los antiguos.
:::

## Después de volver a entrar

Si usaste un código de recuperación porque perdiste tu autenticador para siempre, dedica dos minutos a arreglarlo bien: desactiva la autenticación de dos factores desde tus ajustes de seguridad, y luego vuelve a activarla con tu dispositivo nuevo. Obtendrás un código QR nuevo que escanear y un conjunto nuevo de códigos de recuperación que guardar.

## A dónde ir después

- Configura o reinicia el propio paso del código: @doc(security.twoFactorAuth).
- ¿Bloqueado de otra forma? Consulta @doc(troubleshooting.signIn).
