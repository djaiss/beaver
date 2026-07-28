---
id: kollek.hostingOptions
title: Versão em nuvem versus autohospedagem
slug: nuvem-versus-autohospedagem
section: primeiros-passos
---

# Versão em nuvem versus autohospedagem

Antes de investir tempo no KolleK, é bom saber como você vai executá-lo. Esta página explica suas opções e, tão importante quanto isso, o que você não vai precisar pagar.

## Não existe assinatura

O KolleK não tem planos, não tem níveis e não tem recursos bloqueados por pagamento. Seja qual for a forma escolhida para executá-lo, você recebe o mesmo aplicativo completo, e nada fica trancado atrás de um upgrade.

Existe uma diferença, e ela é sobre tamanho, não sobre recursos. Uma conta hospedada comporta dez itens de graça e para de aceitar novos assim que essa reserva e uma pequena cortesia se esgotam. Desbloqueá-la é um pagamento único. Uma instância autohospedada não tem limite de itens algum. Veja @doc(account.freePlan).

:::note
A autohospedagem é gratuita e ilimitada. Todo recurso está incluído independentemente de como você executa o aplicativo: o que uma conta hospedada paga é a hospedagem, não os recursos.
:::

Então a escolha abaixo é principalmente sobre onde o software é executado, e sobre preferir pagar por um servidor ou pagar para mantermos um funcionando.

## Opção 1: hospedar você mesmo

Esta é a principal forma suportada de executar o KolleK, e é gratuita.

Você executa o KolleK no seu próprio servidor ou computador usando Docker. Seu catálogo, suas fotos e seus documentos vivem todos na infraestrutura que você controla. Você decide para onde vão os backups, e pode mover tudo a qualquer momento.

Autohospedar combina com você se estiver confortável em administrar uma pequena aplicação web, ou disposto a aprender. A configuração é um processo curto e documentado.

Se isso descreve você, o guia de instalação vai estar na seção **Autohospedagem** desta documentação. Por enquanto, o ponto de partida prático é o guia Docker do projeto, em `docker/README.md`.

## Opção 2: usar uma instância hospedada

Se você preferir não executar nada por conta própria, alguém pode oferecer hospedar uma instância do KolleK para você. Esse é um arranjo de conveniência tratado inteiramente fora do aplicativo. Ele não muda o software nem libera nada extra. No nosso próprio serviço hospedado, a conta comporta dez itens de graça antes de precisar ser desbloqueada; outro operador pode organizar as coisas de outro jeito.

Se você usar uma instância hospedada, pode pular a instalação completamente e ir direto para a criação da sua conta.

## Qual escolher

Escolha a autohospedagem se você quer controle total dos seus dados e está satisfeito em administrar um pequeno serviço. Escolha uma instância hospedada se preferir que outra pessoa mantenha o servidor funcionando. De qualquer forma, o aplicativo que você usa é idêntico, e você sempre pode mover seus dados depois, porque pode exportá-los e fazer backup deles.

## Para onde ir agora

- Seja qual for o caminho escolhido, o próximo passo é o mesmo. @doc(accounts.create).
- Curioso sobre com o que você vai trabalhar primeiro? Leia @doc(kollek.whatIs).
