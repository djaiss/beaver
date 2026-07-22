---
id: tutorials.trackValuableItem
title: "Tutorial: Acompanhe a vida completa de um item valioso"
slug: acompanhe-a-vida-de-um-item-valioso
section: tutoriais
---

# Tutorial: Acompanhe a vida completa de um item valioso

A maioria dos itens precisa de uma condição, um local e talvez um preço. Um item genuinamente valioso merece mais: prova do que você pagou, uma opinião profissional sobre seu valor, seguro, a documentação que sustenta tudo isso, e um registro de todos os lugares por onde passou e tudo que foi feito nele. O KolleK registra cada um desses fatos como sua própria entrada datada no exemplar, e este tutorial exercita todos eles em uma única peça.

Vamos acompanhar Priya, que acabou de comprar o melhor relógio da sua coleção, um cronógrafo de 1968. Ao final, o exemplar dele vai carregar uma transação, uma avaliação, um registro de seguro, dois documentos, um empréstimo concluído, um registro de manutenção e uma narrativa de procedência, tudo legível como uma única linha do tempo.

Este é o tutorial mais longo. Faça com um item real seu, ou apenas leia para ver como as peças se encaixam.

## Antes de começar

- Termine primeiro @doc(tutorials.catalogueFirstCollection, "Catalogue sua primeira coleção do início ao fim"). Este tutorial presume que o ciclo central já é natural para você.
- Leia @doc(copyHistory.concept, "O histórico de um exemplar explicado"). É o mapa para tudo o que vem a seguir.
- Lembre-se das duas regras que mantêm o modelo coerente: dinheiro só vive em transações, e reavaliar ou renovar o seguro grava um novo registro em vez de sobrescrever o antigo.

## Passo 1: Catalogue o item e seu exemplar

Priya cria o item "Heuer Carrera 2447" na sua coleção Relógios, que usa o tipo pronto **Relógios**. Ela preenche os campos do tipo: **Marca**, **Modelo**, **Movimento** (Automático, Quartzo ou Manual), e responde **Caixa e papéis** com sim.

Depois ela adiciona o exemplar, e um campo importa mais do que o normal aqui:

- **Identificador.** Ela informa o número de série do relógio. Para itens valiosos, é isso que liga seu registro ao objeto físico, da mesma forma que um número de slab faz para um quadrinho gradeado.
- **Condição** e **local**, como sempre.

Tudo o que vem a seguir acontece na aba **Histórico** deste exemplar, que mostra um exemplar de cada vez.

## Passo 2: Registre a aquisição

::::steps
:::step title="Adicione a transação de compra"
No histórico do exemplar, adicione uma **transação** do tipo **Compra**. Priya informa o valor, a casa de leilão como **contraparte**, a **data**, a comissão do comprador em **taxas**, e o número do lote como **referência**.

::screenshot{label="Formulário de transação preenchido para uma compra em leilão"}
:::
::::

Por que isso importa: este único registro dá ao exemplar seu preço pago e sua data de aquisição, ancora as estatísticas, e mais tarde vai ancorar a narrativa de procedência. Acerte aqui e tudo o mais se apoia nisso. Os detalhes estão em @doc(copies.recordPaymentsAndValue).

## Passo 3: Adicione uma avaliação profissional

Priya manda avaliar o relógio. Ela adiciona uma **avaliação** com o tipo **Avaliação profissional**, o valor avaliado, a confiança definida como **Alta**, e o nome do avaliador como quem fez a avaliação.

:::note
No ano seguinte ela vai mandar avaliar de novo e adicionar uma nova avaliação. A antiga permanece. O valor estimado do exemplar é sempre sua avaliação mais recente, e a sequência de avaliações é como você vai um dia traçar seu valor ao longo do tempo.
:::

## Passo 4: Assegure-o

Com uma avaliação profissional em mãos, o seguro é o próximo passo óbvio. Priya adiciona um @doc(copies.insure, "registro de seguro"): a **seguradora**, o **valor segurado**, o **número da apólice**, o **tipo de cobertura**, a **franquia**, as **datas de início e fim**, se é um **item listado** na apólice, e os dados de contato da seguradora. Ela deixa o status como **Ativo**.

Quando a apólice for renovada, ela vai adicionar um novo registro e marcar este como **Expirado**. Registros expirados e cancelados permanecem visíveis, esmaecidos, atrás do atual, o que é exatamente o que você quer quando um sinistro pergunta qual cobertura existia em determinado ano.

## Passo 5: Anexe a documentação

Registros são afirmações. Documentos são prova. Priya digitaliza dois papéis e os @doc(copies.attachDocuments, "anexa") onde pertencem:

::::steps
:::step title="Anexe o recibo à transação"
Na transação de compra, ela anexa a nota fiscal do leilão como um documento do tipo **Recibo**, com sua data de emissão e o número da nota como referência.
:::

:::step title="Anexe a avaliação ao registro de avaliação"
No registro de avaliação, ela anexa o laudo do avaliador como um documento do tipo **Avaliação**.
:::
::::

Um documento pode ser um arquivo enviado (PDF, imagens, Word, Excel, CSV ou texto simples, até 12 MB) ou um link externo, se a documentação estiver em outro lugar. Anexar cada documento ao registro que ele comprova, em vez de vinculá-lo solto ao exemplar, é o que torna a história auditável depois.

## Passo 6: Empreste para uma exposição, e receba de volta

Uma sociedade local de relojoaria pede para exibir o relógio por um mês. A custódia é exatamente o que os @doc(loans.lendAndBorrow, "empréstimos") acompanham.

::::steps
:::step title="Registre o empréstimo de saída"
Priya cria um **empréstimo** com a direção **Emprestado a terceiros**, a sociedade como a parte envolvida, "Exposição" como o propósito, as datas de empréstimo e vencimento, e a condição do relógio no momento em que saiu das mãos dela.
:::

:::step title="Veja o status do exemplar mudar"
Enquanto o empréstimo está aberto, o exemplar é exibido como emprestado. Ele continua sendo dela, a custódia mudou, não a propriedade. Se a data de vencimento passasse sem a devolução, o KolleK sinalizaria o empréstimo como vencido automaticamente.
:::

:::step title="Registre a devolução"
Quando o relógio volta, ela registra a **devolução**, que captura a data da devolução e a condição em que ele voltou. Comparar a condição de saída e a de entrada é como um dano de transporte se torna visível em vez de discutível.
:::
::::

## Passo 7: Registre a manutenção

Antes de o relógio ir para a exposição, Priya mandou revisá-lo. Ela adiciona um @doc(copies.recordMaintenance, "registro de manutenção") do tipo **Revisão**: um título, o relojoeiro que a realizou, a data, o custo, a condição antes e depois, e uma **próxima data prevista** cinco anos à frente, para que o aplicativo consiga mostrar a próxima revisão quando ela se aproximar. Como uma revisão completa em um movimento vintage é significativa, ela decide incluí-la na procedência do exemplar.

## Passo 8: Construa a narrativa de procedência

Por fim, a história de propriedade. Priya conhece o passado do relógio a partir do catálogo do leilão, e registra isso como @doc(copies.traceProvenance, "eventos de procedência"), do mais antigo ao mais recente:

- Um evento de **Origem** para sua fabricação, datado do ano de 1968.
- Uma **Transferência de propriedade** para a família do dono original, com a precisão de data definida como **Aproximada**, porque o catálogo só diz "cerca de 1975".
- Um evento de **Exposição** para a exibição na sociedade que ela acabou de concluir.
- Sua própria **Aquisição**, datada exatamente, vinculada à transação de compra do passo 2.

Duas coisas a notar. A precisão de data existe porque a procedência costuma ser incerta: um evento pode ser datado exatamente, por mês, por ano, aproximadamente, ou deixado como desconhecido, e é exibido de acordo. E os eventos de procedência não carregam valores: um evento ligado a uma compra ou venda se vincula à sua transação, então o dinheiro fica em um único lugar.

## Passo 9: Leia a história completa

Abra a **linha do tempo** do exemplar. Tudo o que você acabou de registrar, a compra, a avaliação, o seguro, os documentos, o empréstimo de ida e volta, a manutenção e os eventos de procedência, aparece como uma única história cronológica. A visão padrão mantém as entradas significativas, e a visão completa acrescenta as rotineiras. @doc(copyHistory.readTimeline) explica essa visualização em detalhes.

Este é o resultado: uma única tela que responde quanto o relógio custou, quanto vale, quem o teve em mãos, o que foi feito nele, e o que comprova tudo isso.

## Erros comuns a evitar

- **Registrar o preço de compra como uma avaliação.** É uma transação. Essa distinção é a espinha dorsal de todo o modelo.
- **Editar registros antigos em vez de adicionar novos.** Uma nova avaliação é um novo registro de avaliação, uma apólice renovada é um novo registro de seguro. O histórico só funciona se ele se acumula.
- **Deixar documentos sem anexar.** Um recibo anexado à transação que ele comprova é evidência. Um arquivo solto no exemplar é uma digitalização que você vai ter que reidentificar depois.

## Para onde ir depois

- Todo tipo de registro usado aqui tem seu próprio guia detalhado na @doc(copyHistory.index, "seção de histórico do exemplar").
- Veja como esses registros alimentam os números em @doc(insights.collectionStatistics).
- Compartilhando a coleção com outras pessoas? @doc(tutorials.inviteHousehold, "Convide sua família ou clube").
