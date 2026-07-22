---
id: collectionTypes.overview
title: Tipos de coleção e campos personalizados
slug: tipos-de-colecao-e-campos-personalizados
section: conceitos-fundamentais
---

# Tipos de coleção e campos personalizados

Quadrinhos precisam de um número de edição. Vinho precisa de uma safra. Relógios precisam de um mecanismo. O KolleK não tenta adivinhar o que você coleciona, ele deixa você definir isso. Esta página explica as peças: tipos, campos personalizados e grupos de campos.

## Tipos de coleção

Um **tipo de coleção** descreve um tipo de coisa que você coleciona: Quadrinhos, Discos de vinil, Vinho. É o recipiente para os campos personalizados que fazem sentido para esse tipo de coisa.

Os tipos valem para toda a conta e são reutilizáveis. Defina um tipo Quadrinhos uma vez, e qualquer @doc(collections.overview, "coleção") da sua conta pode ativá-lo. Uma coleção pode ativar vários tipos ao mesmo tempo, o que serve bem para coleções mistas: a coleção "Música" de Noah ativa tanto Discos de vinil quanto CD, para que cada item possa ser catalogado como um ou outro.

Quando um item recebe um tipo, seu formulário ganha os campos personalizados que esse tipo define.

## Campos personalizados

Um **campo personalizado** é um detalhe que um tipo pede. Cada campo tem um tipo próprio:

- **Texto**, para qualquer coisa livre, como Editora ou Artista.
- **Número**, para Número da edição ou Ano de lançamento.
- **Data**, para uma data de capa.
- **Sim / Não**, para Assinado ou Primeira edição.
- **Seleção**, um menu suspenso com opções que você define, como uma Nota PSA 10, PSA 9 ou Sem avaliação.
- **Avaliação**, até cinco estrelas, para sua "Minha avaliação" pessoal.

Os valores são registrados por item. O "Amazing Spider-Man #1" de Emma tem Número da edição 1 e Editora Marvel; seus outros quadrinhos compartilham os mesmos campos com seus próprios valores.

## Grupos de campos

Quando um tipo tem muitos campos, **grupos de campos** mantêm o formulário legível. Um grupo é apenas uma seção nomeada: o tipo pronto Quadrinhos agrupa seus campos em "Informações de publicação" e "Condição e avaliação". Formulários longos se tornam seções organizadas em vez de uma lista interminável.

## Os tipos prontos

Uma conta nova já vem com uma dúzia de tipos prontos, para que você não comece do zero: Quadrinhos, Cartas colecionáveis, Discos de vinil, CD, DVD, Moedas, Selos, Livros, Bonecos de ação / Brinquedos, Jogos eletrônicos, Relógios e Vinho, cada um com campos sensatos já agrupados. Use-os como estão, ajuste-os, ou ignore-os e crie os seus.

:::note
Tipos descrevem itens, não exemplares. Um campo que varia por peça física possuída, como condição ou número de série, pertence ao exemplar em vez disso. Veja @doc(items.itemsVsCopies).
:::

## Próximos passos

- Crie ou ajuste um tipo passo a passo em @doc(collectionTypes.setup).
- Compartilhe a definição de um tipo com alguém em @doc(collectionTypes.importExport).
- Veja os campos em ação em @doc(items.addAndEdit).
