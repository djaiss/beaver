---
id: locations.setup
title: Configure seus locais
slug: configure-seus-locais
section: organizacao
---

# Configure seus locais

Um @doc(locations.overview, "local") é onde um exemplar vive fisicamente. Locais se aninham na mesma profundidade que o seu armazenamento real, então "segunda caixa da esquerda, embaixo da janela" se torna algo que você consegue realmente registrar. Um bom mapa de locais é o que permite encontrar um disco específico em uma sala cheia deles.

Locais valem para toda a conta: construa o mapa uma vez e cada coleção o usa. Você precisa da função de editor ou proprietário para gerenciar locais.

## Comece pelos locais padrão

Uma conta recém criada vem com cinco locais iniciais: Sala de Estar 🛋️, Depósito 📦, Vitrine 🗄️, Garagem 🚗 e Escritório 🏢. Renomeie-os, aninhe outros locais debaixo deles, ou exclua-os. Eles existem para que seu primeiro exemplar tenha um lugar para ir.

## Modele seu armazenamento real

O truque é espelhar o mundo físico, de cima para baixo. Noah guarda vinil em dois cômodos, e dentro de cada cômodo há prateleiras, e nas prateleiras há caixas:

- Sala de Estar
  - Prateleira A
    - Caixa 1
    - Caixa 2
  - Prateleira B
- Escritório
  - Armário

::::steps
:::step title="Abra locais"
Vá até as configurações da conta e abra **Locais**.
:::

:::step title="Crie primeiro os lugares de nível mais alto"
Adicione os cômodos ou áreas, deixando o pai vazio. Dê um emoji a cada um se quiser; isso facilita a leitura das listas.

::screenshot{label="Lista de locais com entradas aninhadas"}
:::

:::step title="Aninhe o detalhe embaixo"
Adicione prateleiras, caixas, engradados e pastas, cada um com seu pai definido. Vá tão fundo quanto seu armazenamento realmente vai, e não mais fundo que isso.
:::
::::

Não construa demais. Se tudo está em um único armário, um único local chamado "Armário" já é um mapa perfeitamente bom. Adicione profundidade quando encontrar as coisas ficar difícil, não antes.

## Como os locais são usados

Todo @doc(items.itemsVsCopies, "exemplar") aponta para seu local atual, escolhido quando você cria o exemplar ou sempre que você o @doc(copies.move, "move"). As movimentações ficam registradas ao longo do tempo, então um exemplar lembra não só onde está, mas onde já esteve. Os locais também alimentam a divisão de valor por local nas @doc(insights.collectionStatistics, "estatísticas da coleção"), que é como você descobre que uma vitrine sozinha guarda metade do valor da sua coleção.

## Para onde ir depois

- Coloque o mapa em uso em @doc(copies.track).
- Registre uma movimentação corretamente em @doc(copies.move).
