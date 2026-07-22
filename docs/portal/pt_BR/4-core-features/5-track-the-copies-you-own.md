---
id: copies.track
title: Acompanhe os exemplares que você possui
slug: acompanhe-os-exemplares-que-voce-possui
section: recursos-principais
---

# Acompanhe os exemplares que você possui

Um item sozinho é apenas uma descrição. Um **exemplar** é o seu registro de uma instância física que você realmente possui, com sua própria condição, local, status e histórico. Esta página cobre como adicionar exemplares e cada campo de um exemplar.

A ideia por trás dessa divisão é explicada em @doc(items.itemsVsCopies). Adicionar exemplares exige a função de editor ou proprietário.

## Adicione um exemplar

Exemplares são adicionados no formulário do item, diretamente, para que você possa registrá-los enquanto cataloga.

::::steps
:::step title="Abra o item"
Abra o item e escolha editá-lo, depois adicione um **exemplar**.
:::

:::step title="Registre seu estado físico"
Escolha sua **condição** na lista e escolha o **local** onde ele está guardado.

::screenshot{label="Linha do exemplar, campos de condição e local"}
:::

:::step title="Defina seu status e detalhes"
Deixe o **status** como Possuído para algo que você tem, ou escolha outro status. Preencha os demais campos que se aplicam, depois salve o item.
:::
::::

Tem dois iguais? Adicione um segundo exemplar ao mesmo item, nunca um segundo item. Cada exemplar mantém sua própria condição, local e histórico.

## Os campos do exemplar

- **Identificador.** Um número de série, número de lacre, ou qualquer marca que identifique exatamente este exemplar. Priya registra o número de série gravado em cada um dos seus relógios.
- **@doc(conditions.overview, "Condição").** A classificação deste exemplar, escolhida da lista pronta (Novo, Como Novo, Usado, Desgastado, Danificado, além de qualquer uma que sua conta tenha adicionado).
- **@doc(locations.overview, "Local").** Onde o exemplar vive atualmente. Alterá-lo depois por meio de uma movimentação mantém o histórico; veja @doc(copies.move, "Mova um exemplar").
- **Status.** Em que ponto da sua vida útil o exemplar está. Veja a lista abaixo.
- **Quantidade.** Para exemplares idênticos e intercambiáveis que você não precisa diferenciar, como dez unidades do mesmo booster pack lacrado. Se cada exemplar importa individualmente, dê a cada um sua própria linha em vez disso.
- **Data de descarte.** Quando o exemplar saiu das suas mãos, para status como Vendido ou Descartado.
- **Anotação.** Qualquer coisa que valha a pena lembrar especificamente sobre este exemplar.
- **Valor estimado.** Um número rápido para o que o exemplar vale. Por trás dos panos ele é salvo como uma @doc(copies.recordPaymentsAndValue, "avaliação") do tipo "Estimativa própria", abrindo o histórico de valor do exemplar em vez de ficar preso ao exemplar em si. Para o que realmente importa, adicione avaliações datadas propriamente lá.

## O ciclo de vida do status

- **Possuído.** Em sua posse. O padrão.
- **Encomendado.** Comprado, mas ainda não chegou.
- **Emprestado.** Com outra pessoa, mas ainda seu. A posse mudou, não a propriedade, então o exemplar continua contando como em sua carteira. Empréstimos são melhor registrados por meio de @doc(loans.lendAndBorrow), que define isso automaticamente.
- **Vendido, Doado.** A propriedade passou para outra pessoa.
- **Perdido, Roubado.** Sumiu sem o seu consentimento.
- **Descartado.** Jogado fora ou reciclado.
- **Outro.** Qualquer coisa que a lista não cobre.

Possuído, Encomendado e Emprestado contam como "ainda em sua posse". Os demais registram exemplares que saíram da coleção, mas cujo histórico você quer manter.

## Onde o dinheiro fica

Você deve ter notado que não existe um campo "preço pago" no exemplar. Isso é proposital. O que você pagou, e quando adquiriu o exemplar, vêm das suas **transações**, e o que ele vale ao longo do tempo vem das suas **avaliações**. Isso preserva a história completa do dinheiro em vez de um único número que é sobrescrito. Comece por @doc(copies.recordPaymentsAndValue).

## Para onde ir depois

- Entenda os registros que um exemplar pode carregar: @doc(copyHistory.concept, "O histórico de um exemplar explicado").
- Registre a compra: @doc(copies.recordPaymentsAndValue).
- Mantenha seu endereço atualizado: @doc(copies.move).
