---
id: account.freePlan
title: O plano gratuito e o desbloqueio da sua conta
slug: plano-gratuito-e-desbloqueio
section: conta-e-perfil
---

# O plano gratuito e o desbloqueio da sua conta

Se você mesmo executa o KolleK, esta página não é para você. Uma instância autohospedada não tem limite de itens, nem tela de upgrade, nem nada para comprar. Pule direto para @doc(selfHosting.index).

Numa instância hospedada, aquela que mantemos para quem prefere não administrar um servidor, a conta começa num plano gratuito. Esta página explica quanto esse plano comporta, o que acontece quando você o preenche e o que envolve desbloqueá-lo.

## Quanto o plano gratuito comporta

Uma conta gratuita comporta **dez itens**, contados em todas as suas coleções juntas. Duas coleções de cinco itens preenchem o plano tão rápido quanto uma coleção de dez.

Passados esses dez, o KolleK aceita **mais cinco** como cortesia. Nada muda na conta nesse ponto, exceto o tom das telas: elas dizem com clareza que você passou do plano, e tudo continua funcionando.

Em **quinze itens** a conta para de crescer. Você não consegue adicionar um décimo sexto até a conta ser desbloqueada.

Só os itens são contados. Coleções, categorias, etiquetas, locais, séries, conjuntos, fotos, documentos e as pessoas que você convida não entram na conta, e você pode continuar criando todos eles. Adicionar um exemplar a um item que você já tem também não conta, porque um exemplar pertence a um item em vez de existir sozinho. Veja @doc(items.itemsVsCopies) se essa distinção for novidade para você.

:::note
Nada do que você já catalogou é excluído, escondido ou transformado em somente leitura. Cada item, foto, avaliação e empréstimo fica exatamente onde está, e continua pesquisável e exportável. A única coisa que para é adicionar algo novo.
:::

## O que você vai ver enquanto preenche

**O cartão de uso na barra lateral.** Desde o seu primeiro item, a parte de baixo da barra lateral mostra quanto do plano você já usou, com uma barra e uma contagem. Abaixo de dez, ela simplesmente diz quantos itens faltam. Acima de dez, vira um aviso e diz o quanto você passou.

**Um aviso na tela da coleção.** Depois de passar dos dez itens, abrir qualquer coleção mostra um aviso explicando a sua situação. Dentro da cortesia, ele diz quantos itens você ainda pode adicionar. Em quinze, diz que a adição está pausada.

**O botão Adicionar item para de funcionar.** Com quinze itens, o botão **Adicionar item** de uma coleção fica desabilitado em vez de escondido, para deixar claro que o botão continua existindo e que só a reserva acabou. Todo o resto da tela continua funcionando: você pode editar itens, adicionar exemplares, enviar fotos e registrar empréstimos.

## Desbloquear a conta

Tanto o aviso quanto o cartão de uso levam à tela **Plano e cobrança**, que apresenta os dois caminhos honestos à sua frente.

O primeiro é desbloquear a conta com um pagamento único. Ele remove o limite de itens de vez. Não há assinatura, nem renovação, nem uma segunda fatura, e tudo o que você já catalogou vai junto, intacto.

O segundo é migrar para a autohospedagem, que é gratuita, roda o mesmo código e não tem limite de itens algum. Colocamos os dois na mesma tela de propósito. Veja @doc(kollek.hostingOptions) para os prós e contras de cada um.

:::note
Qualquer pessoa da conta pode ler a tela **Plano e cobrança**, porque qualquer uma pode esbarrar no limite. Só um proprietário pode ir adiante e desbloquear a conta. Se você é editor ou visualizador, peça a um proprietário para assumir daí. Veja @doc(accounts.usersAndRoles).
:::

## A etapa de confirmação

Escolher desbloquear leva um proprietário a uma tela de confirmação, antes de qualquer pagamento. Ela existe porque o pagamento é **definitivo e não reembolsável**, e preferimos dizer isso numa tela que você precisa ler a dizê-lo num contrato que você não vai ler.

A tela pede que você marque quatro caixas, cada uma um ponto distinto a aceitar:

- que este é um pagamento único e que ele não é reembolsável
- que você vai escrever para o suporte em vez de abrir um chargeback no seu banco se algo der errado
- que você sabe que a autohospedagem é gratuita e está escolhendo a conta hospedada de propósito
- que você viu o que o desbloqueio inclui e ele cobre o que você precisa

As quatro precisam estar marcadas antes de você continuar. O que você confirmou fica registrado, junto de quem confirmou, quando, e o endereço de onde a confirmação veio. Quem administra a instância consegue ver esse registro na conta. Ele é guardado como prova do que foi aceito, e não é usado para mais nada.

:::warning
Depois que o pagamento for possível e você o concluir, não há reembolso. Decida antes de pagar, não depois. Na dúvida, o plano gratuito e a autohospedagem dão espaço para você se decidir sem gastar nada.
:::

## O checkout ainda não está aberto

O limite já é aplicado hoje, e as duas telas acima estão prontas, incluindo a etapa de confirmação e o seu registro. O processador de pagamento por trás delas ainda não está conectado, então no momento não há como pagar de verdade nem desbloquear uma conta.

Se você chegar ao limite antes de o checkout abrir, suas opções são remover itens que não quer mais acompanhar, ou migrar para uma instância autohospedada, que é gratuita e ilimitada. Veja @doc(troubleshooting.featureStatus) para o estado atual disto e de tudo o mais que ainda está a caminho.

## Para onde ir agora

- Pesando as duas formas de executar o KolleK? Leia @doc(kollek.hostingOptions).
- Pronto para executá-lo você mesmo? Comece por @doc(selfHosting.installDocker).
- Curioso sobre o que não conta para nada? Veja @doc(accounts.settings).
