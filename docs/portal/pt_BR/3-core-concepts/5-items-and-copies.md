---
id: items.itemsVsCopies
title: Itens versus exemplares
slug: itens-e-exemplares
section: conceitos-fundamentais
---

# Itens versus exemplares

Esta é a página mais importante da documentação. A diferença entre um item e um exemplar é a ideia que torna o KolleK diferente de uma lista qualquer, e quase todas as outras páginas partem do princípio de que você já a conhece. Leva dois minutos para aprender.

## A distinção

Um **item** é o *tipo de coisa*. Um **exemplar** é *uma instância física que você realmente possui*.

"Amazing Spider-Man #1" é um item. O exemplar levemente desgastado na caixa de Emma é um exemplar. O quase perfeito que ela comprou em leilão é outro exemplar. Mesmo item, dois exemplares.

- Possui três unidades do mesmo quadrinho? Isso é **um item com três exemplares**.
- Cada exemplar tem sua própria @doc(conditions.overview, "condição"), seu próprio @doc(locations.overview, "local") de armazenamento, seu próprio valor e seu próprio @doc(copyHistory.concept, "histórico").
- O item contém tudo o que os exemplares têm em comum: o nome, a descrição, as fotos, os valores dos campos personalizados, as tags.

## A regra a lembrar

Detalhes descritivos e de classificação vivem no **item**. Tudo sobre condição, local, dinheiro e histórico vive no **exemplar**.

Pergunte-se: "isso seria verdade para qualquer exemplar dessa coisa?" O roteirista do quadrinho é o mesmo para todo exemplar, então isso pertence ao item. Quanto você pagou é diferente para cada um, então isso pertence ao exemplar.

## Um exemplo prático

Priya cataloga um Omega Speedmaster de 1968:

- O **item** carrega o nome, uma descrição, fotos e campos personalizados como Marca, Modelo e Mecanismo.
- Seu primeiro **exemplar** é classificado como Usado, vive em sua vitrine e carrega o preço que ela pagou em 2019 mais uma avaliação profissional.
- Seu segundo **exemplar**, herdado de seu avô, é classificado como Desgastado, vive em um cofre e carrega um registro de seguro e uma trilha de proveniência que remonta a 1970.

Um relógio como conceito, dois relógios físicos muito diferentes, cada um totalmente acompanhado.

## O que um exemplar registra

Além de condição e local, um exemplar carrega um identificador opcional (um número de série ou de lacre), um status, uma quantidade, uma observação e um valor estimado. O status cobre toda a vida de um exemplar: Possuído, Encomendado, Emprestado, Vendido, Doado, Perdido, Roubado, Descartado ou Outro. Os detalhes estão em @doc(copies.track).

Quanto você pagou e quanto um exemplar vale não são digitados diretamente no exemplar. Eles vêm de suas transações e avaliações, parte de @doc(copyHistory.concept, "o histórico de um exemplar").

## O erro a evitar

:::note
Duas unidades da mesma coisa são dois exemplares de um item, nunca dois itens. Se você está prestes a criar "Amazing Spider-Man #1 (segundo exemplar)", pare e adicione um exemplar ao item existente em vez disso.
:::

Itens duplicados dividem seu histórico e suas estatísticas. Um item com vários exemplares mantém o catálogo organizado e permite que cada peça física conte sua própria história.

## Próximos passos

- Registre seus exemplares em @doc(copies.track).
- Veja o que um exemplar pode guardar em @doc(copyHistory.concept).
- Capture o dinheiro corretamente em @doc(copies.recordPaymentsAndValue).
