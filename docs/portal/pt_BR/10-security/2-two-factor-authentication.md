---
id: security.twoFactorAuth
title: Proteja sua conta com autenticação de dois fatores
slug: autenticacao-de-dois-fatores
section: seguranca
---

# Proteja sua conta com autenticação de dois fatores

A autenticação de dois fatores adiciona uma segunda etapa ao login. Depois que sua senha é aceita, o KolleK pede um código de seis dígitos de um aplicativo autenticador no seu celular. Mesmo que alguém descubra sua senha, essa pessoa não consegue entrar sem esse código.

Este é o controle de segurança mais eficaz que o KolleK oferece, e leva só alguns minutos para configurar.

## O que você vai precisar

Um aplicativo autenticador no seu celular, qualquer um que suporte códigos de uso único baseados em tempo. Se você já escaneou um QR code para proteger outra conta, você já tem um.

## Ative

::::steps
:::step title="Abra suas configurações de segurança"
Vá até seu perfil e abra a área de segurança, depois escolha configurar a **autenticação de dois fatores**.
:::

:::step title="Escaneie o QR code"
O KolleK mostra um QR code. Abra seu aplicativo autenticador, adicione uma nova conta, e escaneie o código. O aplicativo passa a mostrar um código de seis dígitos para o KolleK que muda a cada 30 segundos.

::screenshot{label="Tela de configuração de dois fatores com o QR code"}
:::

:::step title="Confirme com um código"
Digite o código atual de seis dígitos do seu aplicativo no campo de confirmação e envie. Isso comprova que o aplicativo e o KolleK estão sincronizados antes de qualquer mudança na forma como você entra.
:::

:::step title="Salve seus códigos de recuperação"
O KolleK gera oito códigos de recuperação. Copie-os para um lugar seguro que não seja seu celular, como um gerenciador de senhas ou uma página impressa. Cada código pode fazer seu login uma vez, caso você perca seu autenticador.

::screenshot{label="Os oito códigos de recuperação exibidos após a configuração"}
:::
::::

:::warning
Se você perder seu autenticador e não tiver códigos de recuperação, não conseguirá completar a etapa de dois fatores, e pode ficar bloqueado fora do seu usuário. Salve os códigos antes de fechar a página.
:::

## O que muda quando você faz login

A partir de agora, entrar com seu email e senha leva uma etapa extra. Depois que sua senha é aceita, o KolleK pede o código atual do seu aplicativo autenticador. Digite-o e você está dentro.

Se você não conseguir acessar seu aplicativo, digite um dos seus @doc(security.recoveryCodes, "códigos de recuperação") em vez disso.

:::note
Entrar com um @doc(auth.magicLinks, "link mágico") não pede um código de dois fatores. O acesso à sua caixa de entrada já funciona como o segundo fator, então proteja essa caixa de entrada adequadamente.
:::

## Desative

Você pode desativar a autenticação de dois fatores na mesma área de segurança. Fazer isso remove a etapa de código do login e também exclui seus códigos de recuperação e o vínculo com seu aplicativo autenticador. Se você ativar novamente depois, vai escanear um novo QR code e receber um novo conjunto de códigos de recuperação.

## Para onde ir depois

- Confirme que seu plano de recuperação funciona: @doc(security.recoveryCodes).
- Entenda o caminho sem senha e sua consequência: @doc(auth.magicLinks).
- Veja todas as formas de entrar no aplicativo: @doc(auth.signIn).
