---
id: copies.move
title: Mova um exemplar mantendo seu histórico de local
slug: mover-um-exemplar
section: historico-do-exemplar
---

# Mova um exemplar mantendo seu histórico de local

Quando você escolheu um @doc(locations.overview, "local") ao criar um exemplar, você disse ao KolleK onde ele vive. Mover é diferente: muda onde o exemplar vive *enquanto lembra onde ele vivia antes*. Ao longo dos anos, isso se transforma em um rastro de todos os lugares onde o exemplar já foi guardado.

## Como funciona o histórico de local

O histórico de local de um exemplar é uma cadeia de registros. A qualquer momento, exatamente um registro está aberto, e esse registro aberto é o local atual do exemplar. Registrar uma mudança fecha o registro anterior e abre um novo, com a data e o motivo.

Então "onde está" e "onde já esteve" são os mesmos dados, lidos a partir de pontas diferentes.

## Registre uma mudança de local

Priya move seu Omega Speedmaster de 1968 da gaveta da escrivaninha para o cofre depois de sua nova avaliação.

::::steps
:::step title="Abra o histórico do exemplar"
Abra o item, vá até a aba **Histórico**, selecione o exemplar e abra a seção **Locais**.

::screenshot{label="Aba Histórico com a seção Locais aberta"}
:::

:::step title="Escolha para onde ele vai"
Registre uma mudança e escolha o **novo local** entre os locais da sua conta. Se o lugar certo ainda não existir, um proprietário ou editor pode criá-lo antes (veja @doc(locations.setup)).
:::

:::step title="Diga quando e por quê"
Defina a **data** da mudança, um **motivo** ("Avaliação mais alta, movido para o cofre") e qualquer **observação**. Salve.
:::
::::

O exemplar agora mostra o cofre como seu local atual, e a gaveta se tornou histórico, com uma data de início, uma data de término e um motivo para a saída.

## Corrigindo um erro

É possível corrigir um registro passado. Se você digitou uma data errada ou escolheu a prateleira errada, edite o registro em vez de adicionar uma mudança falsa para compensar. O histórico deve refletir o que realmente aconteceu.

:::note
Mover é para relocações físicas reais. Se você está reorganizando o seu próprio armazenamento, renomeando uma prateleira ou aninhando uma caixa em outro lugar, altere o local em @doc(locations.setup) em vez disso. Os exemplares continuam apontando para ele, e nenhum registro de mudança é necessário.
:::

## Por que se importar com o histórico

Para exemplares do dia a dia, o local atual é tudo que você vai precisar verificar. O histórico mostra seu valor com os valiosos: uma seguradora pode perguntar onde uma peça foi guardada, um comprador pode se importar que ela passou uma década em um armário climatizado, e você pode simplesmente querer saber onde algo estava no ano em que foi arranhado. Como as mudanças têm data, o histórico de local se alinha com empréstimos, manutenções e mudanças de condição na @doc(copyHistory.readTimeline, "linha do tempo").

## Próximos passos

- Construa um mapa de armazenamento que valha a pena consultar em @doc(locations.setup).
- Um exemplar que sai das suas mãos é um empréstimo, não uma mudança de local. Veja @doc(loans.lendAndBorrow).
- Veja as mudanças de local na história completa em @doc(copyHistory.readTimeline).
