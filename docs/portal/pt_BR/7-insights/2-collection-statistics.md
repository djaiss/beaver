---
id: insights.collectionStatistics
title: Entenda as estatísticas da sua coleção
slug: estatisticas-da-colecao
section: insights
---

# Entenda as estatísticas da sua coleção

Toda coleção tem uma tela de estatísticas que transforma sua entrada de dados em respostas: quanto vale, como cresceu e onde o valor está concentrado. Esta página explica cada número e, igualmente importante, de onde cada um vem, para que você possa confiar no que lê.

## De onde vêm os números

Duas regras impulsionam quase tudo nessa tela. Elas vêm de @doc(copyHistory.concept, "como funciona o histórico de um exemplar"):

- **O valor atual de um exemplar é sua @doc(copies.recordPaymentsAndValue, "avaliação") mais recente.** Um exemplar que nunca foi avaliado conta como não avaliado, não como valendo zero por suposição.
- **A data de aquisição de um exemplar vem de sua @doc(copies.recordPaymentsAndValue, "transação") de aquisição mais antiga**, como uma compra, troca, presente recebido ou herança. Um exemplar sem essa transação não tem data de aquisição, então não aparece nos gráficos baseados em tempo. A tela informa quantos exemplares estão sem data, para que você saiba o que os gráficos estão deixando de fora.

Se um gráfico parecer mais vazio do que sua coleção realmente é, isso é a estatística pedindo mais entrada de dados, não um erro.

## Os totais

No topo: a **contagem de itens**, a **contagem de exemplares**, o **valor total estimado** (a soma do valor atual de cada exemplar) e o **valor médio por item**. Você também verá o que mudou recentemente: itens adicionados neste mês e valor adicionado neste mês.

## Conclusão de sets

Se a coleção tem @doc(sets.trackCompletion, "sets com uma meta definida"), a tela os resume: quantas peças você possui em relação à meta combinada, e a porcentagem de conclusão. Apenas sets com meta acima de zero participam. Um set que possui mais que sua meta conta como completo, não como além do completo.

## Valor ao longo do tempo

Um gráfico de doze meses do valor estimado acumulado da sua coleção, mês a mês. Cada exemplar entra na linha na sua data de aquisição, com seu valor atual. Tudo adquirido antes da janela de doze meses já está incluído no primeiro ponto, então a linha começa a partir do seu total real, não de zero.

## Aquisições por mês

Quantos exemplares você adquiriu em cada um dos últimos doze meses, com base nas mesmas datas de aquisição. Um gráfico parado geralmente indica transações de aquisição faltando, não um ano parado.

## Distribuições

- **Por categoria.** Como os itens se distribuem entre suas @doc(categories.organizeItems, "categorias"). As seis maiores categorias são nomeadas, o restante entra em "Outros", e itens sem categoria aparecem como sua própria fatia.
- **Por condição.** Como seus exemplares se classificam, em contagens e porcentagens por @doc(conditions.overview, "condição").
- **Valor por local.** O valor somado dos exemplares em cada @doc(locations.overview, "local"), para que você saiba o que está guardado onde. Priya usa isso para ver quanto valor está na sua vitrine em comparação com o cofre. Apenas locais que guardam valor aparecem.

## Itens principais

Os cinco itens mais valiosos da coleção, classificados pelo valor atual combinado de seus exemplares, cada um mostrado com a condição e o local de seu exemplar mais valioso.

## Próximos passos

- Alimente os gráficos: @doc(copies.recordPaymentsAndValue).
- Acompanhe a conclusão corretamente: @doc(sets.trackCompletion).
- Veja a visão de toda a conta: @doc(insights.dashboard).
