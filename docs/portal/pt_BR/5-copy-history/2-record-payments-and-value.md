---
id: copies.recordPaymentsAndValue
title: Registre o que você pagou e quanto vale
slug: registrar-pagamentos-e-valor
section: historico-do-exemplar
---

# Registre o que você pagou e quanto vale

Dinheiro e valor são as duas perguntas que colecionadores mais fazem, e o KolleK as mantém propositalmente separadas. Uma **transação** registra dinheiro que realmente mudou de mãos. Uma **avaliação** registra quanto um exemplar vale em um determinado momento, tenha ou não havido movimentação de dinheiro. Esta página mostra como registrar as duas coisas e explica a regra que mantém tudo organizado.

Se você ainda não leu @doc(copyHistory.concept, "O histórico de um exemplar explicado"), leia primeiro. Ela apresenta a ideia de que esses registros formam um histórico somente de acréscimo, não campos que você sobrescreve.

## A regra que mantém tudo organizado

Um preço de compra é uma transação, não uma avaliação.

Quando Priya compra um Omega Speedmaster de 1968 por 4.200, isso é uma transação do tipo **Compra**. Ela registra o que Priya pagou naquele dia, e isso nunca muda. Quanto o relógio *vale* é uma pergunta separada que muda ao longo do tempo, e cada resposta é uma avaliação própria.

O KolleK deriva dois números desses registros automaticamente:

- O **valor estimado** de um exemplar é o valor de sua avaliação mais recente. Um exemplar sem avaliações aparece como não avaliado, não como valendo zero.
- O **preço pago** e a **data de aquisição** de um exemplar vêm de sua transação de aquisição mais antiga (uma Compra, Troca, Presente recebido ou Herança).

Você nunca digita esses números diretamente no exemplar. Você registra o histórico, e os números atuais decorrem dele.

## Registre uma transação

Uma transação cobre qualquer movimentação de dinheiro ou propriedade em torno de um exemplar: comprá-lo, vendê-lo, trocá-lo, pagar uma taxa ou enviá-lo para algum lugar.

::::steps
:::step title="Abra o histórico do exemplar"
Abra o item, vá até a aba **Histórico** e selecione o exemplar desejado. Em seguida, abra a seção **Transações**.

::screenshot{label="Aba Histórico com a seção Transações aberta"}
:::

:::step title="Adicione uma transação"
Escolha adicionar uma transação e selecione seu **tipo**: Compra, Venda, Troca, Presente recebido, Presente dado, Herança, Reembolso, Taxa, Imposto, Frete ou Outro.
:::

:::step title="Informe o valor"
Preencha o **valor** e, opcionalmente, **impostos**, **taxas** e **frete**, para capturar o custo total real, não apenas o preço de etiqueta.
:::

:::step title="Adicione o contexto"
Registre a **contraparte** (de quem você comprou ou para quem vendeu), a **data** e uma **referência**, como um número de pedido ou lote de leilão. Salve a transação.
:::
::::

Priya registra a compra de seu Speedmaster: tipo **Compra**, valor 4.200, taxas de 120 para a casa de leilões, contraparte "Fine Time Auctions" e o número do lote como referência. Esse único registro agora responde o que ela pagou, quando adquiriu o relógio e de onde ele veio.

:::note
A transação de aquisição mais antiga (Compra, Troca, Presente recebido ou Herança) é o que dá ao exemplar sua data de aquisição. Exemplares sem uma são contados como sem data em suas estatísticas, então registre até mesmo coisas que você comprou há muito tempo, com sua melhor estimativa da data.
:::

## Registre uma avaliação

Uma avaliação responde "quanto isso vale agora, e o quanto tenho certeza disso".

::::steps
:::step title="Abra a seção Avaliações"
Na mesma aba **Histórico**, com seu exemplar selecionado, abra a seção **Avaliações**.
:::

:::step title="Adicione uma avaliação"
Escolha um **tipo de avaliação**: Estimativa própria, Avaliação profissional, Estimativa de mercado, Valor de seguro, Estimativa de leilão, Estimativa automática ou Outro.
:::

:::step title="Informe o valor e sua confiança"
Preencha o **valor**, escolha um nível de **confiança** (Baixa, Média, Alta ou Desconhecida) e registre **quem avaliou**. Salve.

::screenshot{label="Formulário de nova avaliação com tipo, valor e confiança"}
:::
::::

Dois anos depois, um negociante diz a Priya que o Speedmaster valeria cerca de 5.500. Ela adiciona uma nova avaliação: **Estimativa de mercado**, 5.500, confiança **Média**, avaliado pelo negociante. Sua avaliação original permanece no histórico, e o valor estimado do exemplar é atualizado para o novo número.

:::note
Reavaliar sempre grava uma nova avaliação. Você nunca edita a antiga para um novo número, então mantém um registro genuíno de como o valor mudou ao longo do tempo. Esse histórico é o que desenha o gráfico de valor ao longo do tempo em suas estatísticas.
:::

## Onde esses números aparecem

Os números que você registra aqui alimentam o restante do KolleK: o valor total mostrado em cada coleção, os gráficos de valor ao longo do tempo e de aquisições em @doc(insights.collectionStatistics, "estatísticas da coleção"), e os itens de maior valor. Transações e avaliações bem feitas são o que tornam essas telas confiáveis.

## Próximos passos

- Guarde a documentação junto do registro. @doc(copies.attachDocuments), como o recibo em uma transação ou o laudo em uma avaliação.
- Vai segurar o exemplar por esse valor? @doc(copies.insure).
- Construindo a história completa de propriedade? @doc(copies.traceProvenance).
