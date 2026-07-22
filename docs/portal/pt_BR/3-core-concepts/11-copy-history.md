---
id: copyHistory.concept
title: O histórico de um exemplar
slug: historico-de-um-exemplar
section: conceitos-fundamentais
---

# O histórico de um exemplar

Esta página explica o coração conceitual do KolleK: um exemplar mostra seu estado atual, enquanto tudo que já aconteceu com ele vive em registros separados e datados. Entenda isso uma vez, e toda a seção de acompanhamento se torna um conjunto de tarefas óbvias.

## Estado atual versus histórico

Olhe para um dos relógios de Priya. O exemplar mostra seu estado atual rapidamente: sua @doc(conditions.overview, "condição") é Usado, seu @doc(locations.overview, "local") atual é a vitrine, seu valor estimado é o que a última avaliação disse.

Nada disso é digitado como um fato simples que sobrescreve o anterior. Cada um é a ponta visível de um registro por baixo:

- O valor estimado é sua **avaliação mais recente**.
- O preço que ela pagou, e a data em que adquiriu, vêm de sua **transação de aquisição mais antiga**.
- O local atual é a **entrada aberta em seu histórico de local**.

O exemplar é um resumo. Os registros são a verdade.

## Os tipos de registro

Sete tipos de registros datados podem ser associados a um exemplar, cada um com seu propósito próprio e sua própria página de instruções:

- **Transações** registram dinheiro e mudanças de propriedade: o que você pagou, por quanto vendeu, taxas, frete. Veja @doc(copies.recordPaymentsAndValue).
- **Avaliações** registram quanto o exemplar valia em um determinado momento, e quem disse isso. Mesma página das transações, porque as duas são fáceis de confundir.
- **Registros de seguro** capturam a cobertura: seguradora, valor segurado, datas da apólice. Veja @doc(copies.insure).
- **Empréstimos** acompanham a posse quando um exemplar sai das suas mãos ou chega das mãos de outra pessoa. Veja @doc(loans.lendAndBorrow).
- **Registros de manutenção** documentam trabalhos de limpeza, reparo e conservação. Veja @doc(copies.recordMaintenance).
- **Eventos de proveniência** constroem a história de propriedade e autenticidade. Veja @doc(copies.traceProvenance).
- **Histórico de local** lembra todos os lugares onde o exemplar já viveu. Veja @doc(copies.move, "Mova um exemplar").

Você também pode @doc(copies.attachDocuments, "anexar documentos") (recibos, avaliações, certificados) ao exemplar ou a qualquer registro individual, e ler tudo reunido em @doc(copyHistory.readTimeline, "a linha do tempo do exemplar").

## As duas regras que mantêm a coerência

**Dinheiro sempre vive nas transações.** Um preço de compra é uma transação. Uma venda é uma transação. Avaliações e eventos de proveniência descrevem valor e história, nunca pagamento.

**O histórico é somente de acréscimo.** Reavaliar um exemplar grava uma nova avaliação ao lado da antiga. Renovar o seguro grava um novo registro. Nada sobrescreve o passado, e é por isso que a linha do tempo consegue contar a história completa anos depois.

:::note
Se você se pegar editando um registro antigo para refletir algo novo, pare e adicione um novo registro em vez disso. Editar serve para corrigir erros, não para atualizar a realidade.
:::

## Você precisa de tudo isso?

Não. Emma cataloga a maioria dos quadrinhos apenas com um exemplar, uma condição e um local. Os registros de histórico mostram seu valor nas peças que importam: as valiosas, as seguradas, as emprestadas e as herdadas. Use tanto ou tão pouco quanto cada exemplar merecer.

## Próximos passos

- Comece pelo dinheiro em @doc(copies.recordPaymentsAndValue).
- Veja toda a história em uma única visão em @doc(copyHistory.readTimeline).
- Acompanhe uma peça valiosa do início ao fim no tutorial @doc(tutorials.trackValuableItem, "Acompanhe a vida completa de um item valioso").
