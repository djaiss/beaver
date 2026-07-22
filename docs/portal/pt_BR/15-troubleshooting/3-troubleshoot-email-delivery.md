---
id: troubleshooting.emailDelivery
title: Solução de problemas de entrega de e-mail
slug: solucao-de-problemas-de-entrega-de-e-mail
section: solucao-de-problemas
---

# Solução de problemas de entrega de e-mail

Você convidou alguém e nada chegou. Você solicitou um link mágico e sua caixa de entrada continua vazia. Esta página explica por que e-mails esperados somem e como descobrir o que realmente aconteceu.

## A causa mais comum: uma instância nova não envia e-mail

Em uma instância recém auto-hospedada, o serviço de e-mail do KolleK, por padrão, **registra os e-mails em vez de enviá-los**. Todo e-mail é composto e registrado, mas nada sai do servidor até que um operador configure um serviço de e-mail real.

Isso é proposital, para que uma instância não configurada nunca falhe silenciosamente nem envie spam por acidente. Mas isso significa que, em uma instalação nova, convites, links mágicos, redefinições de senha e alertas de segurança parecem simplesmente desaparecer.

:::note
Se ninguém configurou o e-mail na sua instância ainda, nenhum e-mail vai chegar, para ninguém, nunca. Essa é a primeira coisa a verificar.
:::

**Se você opera a instância**, configure SMTP ou Resend seguindo @doc(selfHosting.setupEmailDelivery).

**Se outra pessoa opera a instância**, aponte essa pessoa para essa página. Não há nada que você possa mudar de dentro do aplicativo.

## Verifique o que foi realmente enviado

O KolleK registra todo e-mail que envia a você, com seu status de entrega. Vá até seu perfil e abra seu histórico de **e-mails enviados**. Cada item mostra quando foi enviado e, quando o rastreamento está disponível, se foi entregue ou rejeitado.

Como interpretar o que você encontrar:

- **O e-mail está listado e marcado como entregue.** O KolleK fez sua parte. Confira sua pasta de spam e procure na sua caixa de entrada pelo endereço do remetente.
- **O e-mail está listado e marcado como rejeitado.** Seu provedor de e-mail o recusou. Confira se seu endereço está correto no seu perfil, e se seu provedor está bloqueando a instância.
- **O e-mail está listado sem informação de entrega.** Em instâncias que enviam por SMTP simples, o rastreamento de entrega não está disponível, então isso é normal. A ausência de rejeição é um bom sinal.
- **O e-mail não está listado de forma alguma.** Ele nunca foi composto, o que geralmente significa que a ação não foi concluída. Tente a ação de novo.

Detalhes completos sobre essa tela em @doc(activity.logAndSentEmails, "Seu histórico pessoal de atividades e e-mails enviados").

## Um convite nunca chegou ao convidado

O e-mail de convite vai para o convidado, então ele nunca aparece no seu próprio histórico de envios. Peça ao convidado para checar o spam, confirme que você digitou o e-mail dele corretamente, e lembre-se de que convites expiram depois de sete dias. Na dúvida, envie um novo. Em uma instância nova, verifique a configuração de e-mail primeiro, como descrito acima.

## Verificação, redefinições e links mágicos caem no spam

E-mails transacionais de uma pequena instância auto-hospedada são exatamente o tipo de coisa que filtros de spam desconfiam. Marcar uma mensagem como "não é spam" geralmente ensina seu provedor. Operadores podem melhorar a capacidade de entrega com uma configuração adequada do remetente, coberta em @doc(selfHosting.setupEmailDelivery).

## Próximos passos

- Configuração do operador para entrega real: @doc(selfHosting.setupEmailDelivery).
- Seu histórico pessoal de e-mails: @doc(activity.logAndSentEmails, "Seu histórico pessoal de atividades e e-mails enviados").
- O que cada e-mail é e quando é disparado: @doc(reference.emailsSent).
