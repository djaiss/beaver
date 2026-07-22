---
id: locations.overview
title: Locais
slug: locais
section: conceitos-fundamentais
---

# Locais

Um local responde a pergunta que toda coleção em crescimento acaba fazendo: "onde foi que eu coloquei isso?" Esta página explica como o KolleK modela o armazenamento físico.

## O que é um local

Um local é um lugar onde um @doc(items.itemsVsCopies, "exemplar") vive fisicamente: um cômodo, uma prateleira, uma caixa, um cofre. Cada local pode carregar um emoji para ser identificado rapidamente nas listas.

Locais se aninham tão profundamente quanto você precisar, então podem espelhar o seu espaço real. Noah modela o seu assim: Sala de estar, depois Prateleira A dentro dela, depois Caixote 3 dentro dela. Quando ele quer saber onde está um disco, a resposta é tão precisa quanto seu mapa.

Locais valem para a conta inteira. Defina "Vitrine" uma vez e toda coleção pode guardar exemplares ali, o que reflete a realidade: uma prateleira pode conter quadrinhos e moedas lado a lado.

## Locais se anexam a exemplares, não a itens

Um item é uma ideia, então é o exemplar que fica em algum lugar. Os dois exemplares do mesmo quadrinho de Emma vivem em lugares diferentes: um em Caixa longa 1, outro emoldurado na parede. Cada exemplar aponta para seu próprio local atual.

Uma conta nova vem com alguns locais iniciais (Sala de estar, Armazenamento, Vitrine, Garagem, Escritório). Renomeie-os, aninhe algo dentro deles, ou substitua-os pelos seus próprios.

## Mudanças são lembradas

Quando você move um exemplar, o KolleK não simplesmente sobrescreve o local antigo. Ele registra a mudança, então o exemplar mantém um rastro de todos os lugares onde já esteve e quando. O local atual é simplesmente a entrada mais recente desse rastro. Isso faz parte de @doc(copyHistory.concept, "o histórico de um exemplar"), e o passo a passo está em @doc(copies.move).

## Próximos passos

- Construa seu mapa de armazenamento em @doc(locations.setup).
- Mova as coisas corretamente em @doc(copies.move).
- Veja onde os locais aparecem no formulário do exemplar em @doc(copies.track).
