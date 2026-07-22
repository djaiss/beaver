---
id: security.recoveryCodes
title: Salve e use seus códigos de recuperação
slug: codigos-de-recuperacao
section: seguranca
---

# Salve e use seus códigos de recuperação

Os códigos de recuperação são o seu caminho de volta caso você perca seu autenticador. Quando você ativa a @doc(security.twoFactorAuth, "autenticação de dois fatores"), o KolleK gera oito deles. Cada código funciona exatamente uma vez, no lugar de um código do seu aplicativo.

Celulares se perdem, quebram e são trocados. Os códigos de recuperação são o que separa esse dia ruim comum de ficar bloqueado fora do seu catálogo.

## Onde você os consegue

Os códigos são mostrados logo depois que você confirma a configuração de dois fatores. Esse é o momento em que você deve salvá-los.

Bons lugares para guardá-los:

- Um gerenciador de senhas, nas anotações da sua entrada do KolleK.
- Uma página impressa em uma gaveta em casa.
- Um arquivo criptografado que você mantém em backup.

Um lugar ruim para guardá-los é apenas no seu celular, porque a situação em que você precisa deles é justamente a situação em que seu celular sumiu.

:::warning
Se você perder tanto seu autenticador quanto seus códigos de recuperação, não conseguirá completar a etapa de dois fatores e pode ficar bloqueado fora do seu usuário. Não há uma forma automática de contornar isso, então guarde os códigos em um lugar seguro agora.
:::

## Use um código para entrar

Quando o KolleK pedir seu código de seis dígitos do autenticador e você não puder fornecer um:

1. Na tela do desafio de dois fatores, digite um dos seus códigos de recuperação no lugar do código do aplicativo.
2. Você entra normalmente.

É só isso. O desafio aceita tanto um código atual do autenticador quanto um código de recuperação não utilizado.

## Cada código funciona uma vez

Um código de recuperação é consumido no momento em que você o usa. Ele nunca mais vai funcionar, e seus códigos restantes continuam válidos. Risque os códigos usados onde quer que você os tenha guardado.

:::note
Se você estiver com poucos códigos restantes, ou suspeitar que alguém mais os viu, desative a autenticação de dois fatores e ative-a novamente. Reativar gera um novo conjunto de oito códigos e invalida os antigos.
:::

## Depois de voltar a entrar

Se você usou um código de recuperação porque perdeu seu autenticador de vez, reserve dois minutos para organizar tudo direito: desative a autenticação de dois fatores nas suas configurações de segurança, depois ative-a novamente com seu novo dispositivo. Você vai receber um novo QR code para escanear e um novo conjunto de códigos de recuperação para salvar.

## Para onde ir depois

- Configure ou refaça a etapa de código em si: @doc(security.twoFactorAuth).
- Ficou bloqueado de outra forma? Veja @doc(troubleshooting.signIn).
