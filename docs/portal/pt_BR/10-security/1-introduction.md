---
id: security.index
title: Visão geral de segurança
slug: seguranca
section: seguranca
---

# Visão geral de segurança

O KolleK guarda registros que importam para você: o que você possui, quanto vale e onde fica. Esta página mapeia os controles que mantêm seu usuário e seus dados protegidos, para que você decida quais ativar. Todos eles são opcionais. A maioria vale os cinco minutos que você vai gastar com eles.

## Sua senha

Toda conta começa com uma senha. O KolleK aplica duas regras ao definir uma: ela precisa ter pelo menos oito caracteres, e é verificada contra listas de senhas conhecidas por terem vazado em violações anteriores. Se uma senha que você tentar for recusada, é porque ela apareceu em uma dessas listas, então escolha algo que você ainda não tenha usado em outro lugar.

Você pode alterar sua senha a qualquer momento, e recuperar o acesso caso a esqueça. Veja @doc(auth.resetPassword).

## Autenticação de dois fatores

A maior melhoria que você pode fazer. Com a autenticação de dois fatores ativada, entrar com sua senha também pede um código de seis dígitos de um aplicativo autenticador no seu celular. Uma senha roubada sozinha deixa de ser suficiente para entrar.

Configure em @doc(security.twoFactorAuth), e entenda bem os @doc(security.recoveryCodes, "códigos de recuperação") antes de depender dela.

## Códigos de recuperação

Quando você ativa a autenticação de dois fatores, o KolleK gera oito códigos de recuperação. Cada um pode ser usado uma única vez, no lugar de um código do autenticador, para você voltar a entrar caso perca o celular. Guarde-os em um lugar seguro. @doc(security.recoveryCodes) explica como.

## Links mágicos

Uma forma de entrar sem senha. O KolleK envia por email um link que faz seu login diretamente, válido por cinco minutos. É conveniente, mas com uma consequência que vale entender: um link mágico não pede o código de dois fatores, porque o acesso à sua caixa de entrada já funciona como o segundo fator. @doc(auth.magicLinks) explica quando usá-los.

## Chaves de API

Se você usa a API do KolleK, a autenticação é feita com chaves de API pessoais. Elas são criadas e revogadas a partir do seu perfil, e o KolleK te envia um email sempre que uma é criada ou excluída, então uma chave que você não criou nunca passa despercebida. Veja @doc(apiKeys.manage).

## Emails de alerta

O KolleK fica de olho em eventos que valem um aviso: uma tentativa de login que falhou, um login a partir de um novo dispositivo, uma mudança no seu endereço IP, uma chave de API criada ou excluída. Quando algo assim acontece, você recebe um email. @doc(security.alertEmails) explica o que cada alerta significa e o que fazer a respeito.

## Uma configuração sensata

Se você só for fazer duas coisas, faça estas:

1. Ative a @doc(security.twoFactorAuth, "autenticação de dois fatores").
2. Guarde seus @doc(security.recoveryCodes, "códigos de recuperação") em algum lugar que não seja o seu celular.

Todo o resto desta seção pode esperar até você precisar.

## Páginas nesta seção

1. @doc(security.twoFactorAuth)
2. @doc(security.recoveryCodes)
3. @doc(auth.magicLinks)
4. @doc(auth.resetPassword)
5. @doc(security.alertEmails)
6. @doc(apiKeys.manage)
