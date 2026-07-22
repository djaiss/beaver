---
id: copies.traceProvenance
title: Rastreie a proveniência de um exemplar
slug: rastrear-proveniencia
section: historico-do-exemplar
---

# Rastreie a proveniência de um exemplar

Proveniência é a história de onde um exemplar veio: quem já o possuiu, onde já foi exibido, quando foi autenticado e como chegou até você. Para peças valiosas ou historicamente interessantes, essa história faz parte do valor. O KolleK permite construí-la como uma sequência de eventos de proveniência datados, que se lê, do mais antigo ao mais recente, como uma narrativa.

Diferente dos outros registros desta seção, a proveniência costuma remontar a muito antes de você possuir o exemplar, a décadas que você só conhece pela metade. O modelo foi criado para lidar com essa incerteza.

## O que um evento de proveniência registra

Cada evento tem um **tipo**, um **título** e o máximo de contexto que você tiver: as **partes** envolvidas, o **local**, uma **referência** (um número de catálogo, um lote de leilão, um registro de arquivo) e uma **data**.

Os tipos de evento cobrem a vida de um objeto: **Aquisição**, **Venda**, **Presente**, **Herança**, **Transferência de propriedade**, **Transferência de posse**, **Empréstimo**, **Devolução**, **Exposição**, **Autenticação**, **Avaliação**, **Restauração significativa**, **Origem**, **Descoberta** e **Outro**.

Dois deles ancoram as extremidades da história. **Origem** registra onde o objeto começou (sua fabricação, sua impressão, sua cunhagem). **Descoberta** registra o momento em que ele veio à tona, quando isso é uma história por si só.

## Datas sobre as quais você não tem certeza

Datas de proveniência costumam ser aproximadas, e fingir o contrário corromperia a história. Cada evento carrega uma **precisão de data** junto de sua data:

- **Data exata**. Você sabe o dia.
- **Mês**. Você sabe o mês e o ano.
- **Ano**. Você sabe apenas o ano.
- **Aproximada**. Uma estimativa da melhor forma possível. Leia como "por volta de".
- **Desconhecida**. O evento aconteceu, mas você não consegue datá-lo.

O evento é exibido de acordo com sua precisão, então "por volta de 1970" e "março de 1970" aparecem com o grau de certeza que realmente têm.

## A regra do dinheiro

:::note
Eventos de proveniência não carregam valores monetários. Dinheiro sempre vive nas transações. Um evento ligado a uma compra ou venda se conecta à sua transação em vez disso, para que a narrativa e a contabilidade nunca se distanciem.
:::

Essa é a mesma regra que você encontrou em @doc(copies.recordPaymentsAndValue), vista pelo outro lado.

## Construa uma narrativa de proveniência

O Omega Speedmaster de 1968 de Priya veio com uma pasta de documentos da casa de leilões. Ela reconstrói sua história.

::::steps
:::step title="Abra o histórico do exemplar"
Abra o item, vá até a aba **Histórico**, selecione o exemplar e abra a seção **Proveniência**.

::screenshot{label="Aba Histórico com a seção Proveniência aberta"}
:::

:::step title="Comece pela origem"
Adicione um evento de **Origem**: "Fabricado, Bienne, Suíça", datado de 1968 com precisão de **Ano**.
:::

:::step title="Adicione o que a documentação sustenta"
Adicione uma **Transferência de propriedade** para o primeiro proprietário conhecido, com data **Aproximada** do início dos anos 1970, com o nome da parte a partir dos documentos de manutenção. Adicione um evento de **Autenticação** para o extrato dos arquivos do fabricante, com o número do extrato como **referência**.
:::

:::step title="Termine com sua aquisição"
Adicione um evento de **Aquisição** para sua própria compra, com a data exata, e vincule-o à transação de compra que ela já registrou. O preço fica na transação, não aqui.
:::
::::

Lida de cima para baixo, a seção agora conta a história do relógio desde a oficina suíça até a coleção de Priya.

## Verificado ou lenda de família

Cada evento carrega uma marcação de **verificado**, com uma nota de como foi verificado. Use-a com honestidade. Um extrato de arquivo é evidência verificada. "Meu avô sempre disse que comprou em Genebra" também é parte real da história, mas permanece não verificada, e a narrativa fica mais forte por admitir a diferença.

## Eventos que chegam sozinhos

Parte da proveniência se constrói sozinha. Um @doc(loans.lendAndBorrow, "empréstimo") marcado como parte da proveniência adiciona os eventos correspondentes de empréstimo e devolução, e um @doc(copies.recordMaintenance, "registro de manutenção") sinalizado como significativo aparece como um evento de restauração. Você monta o passado distante; o presente se documenta sozinho, à medida que acontece.

## Próximos passos

- Anexe o extrato de arquivo ou o certificado ao seu evento em @doc(copies.attachDocuments).
- Registre a compra à qual o evento de aquisição se conecta em @doc(copies.recordPaymentsAndValue).
- Leia a história completa em @doc(copyHistory.readTimeline).
