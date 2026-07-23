---
id: loans.custody
title: Empréstimos e custódia
slug: emprestimos-e-custodia
section: recursos-principais
---

# Empréstimos e custódia

Um empréstimo é uma transferência temporária de custódia sem qualquer transferência de propriedade. Quando você empresta uma peça a um amigo, a uma galeria ou a um museu, ela continua sendo sua. Quando você toma uma peça emprestada, ela continua pertencendo a outra pessoa. A seção **Empréstimos** é a visão de toda a conta sobre tudo o que está no momento fora das suas mãos ou nas suas mãos a título de empréstimo.

Consultar a seção está aberto a qualquer papel. Registrar, devolver, editar e excluir um empréstimo exige o papel de **editor** ou **proprietário**.

## As duas direções

Cada empréstimo aponta para um de dois sentidos, e a seção mostra uma direção por vez. Use o seletor no topo para alternar entre elas.

- **Emprestado a alguém.** Uma peça sua que outra pessoa está guardando. Enquanto um empréstimo de saída está ativo ou atrasado, o exemplar dele aparece como **Emprestado** na sua coleção, porque não está fisicamente com você.
- **Tomado emprestado.** Uma peça que pertence a outra pessoa e que você está guardando por ora. Uma peça tomada emprestada nunca muda como os seus próprios exemplares aparecem, porque ela nunca foi sua.

## O que as abas mostram

Dentro de uma direção, as abas recortam os mesmos empréstimos de maneiras diferentes.

- **Todos os empréstimos.** Cada empréstimo da direção, com uma caixa de busca e filtros por coleção, situação e ordem de classificação.
- **A vencer e vencidos.** Três listas: empréstimos que passaram da data de vencimento, empréstimos que vencem dentro de trinta dias, e empréstimos sem prazo que não têm data de vencimento alguma.
- **Riscos e exceções.** Os empréstimos que pedem uma segunda olhada: vencidos, perdidos, devolvidos em pior estado, sem data de vencimento, sem estado registrado na saída, ou emprestados sem nenhum documento no arquivo.
- **Por parte.** Um cartão por pessoa ou instituição, para que você veja de uma vez tudo o que um mesmo tomador ou emprestador está com.
- **Cauções.** O que você retém ou o que lhe é devido no conjunto dos empréstimos em aberto, e os empréstimos que trazem uma caução.
- **Linha do tempo.** Próximos vencimentos, peças devolvidas recentemente e peças emprestadas recentemente.

Os blocos de estatísticas no topo são atalhos: cada um abre a aba que responde ao seu número.

## Registrar um empréstimo

Você pode iniciar um empréstimo direto da seção, sem precisar procurar o exemplar antes.

::::steps
:::step title="Abrir o painel de novo empréstimo"
Escolha **Novo empréstimo**. Selecione a direção e depois desça da coleção ao item, até o exemplar exato que está se movendo.
:::

:::step title="Nomear a parte e as datas"
Informe para quem a peça vai ou de quem ela vem, a data em que saiu e uma data de vencimento. Marque **sem prazo** quando não houver uma data de devolução combinada.
:::

:::step title="Registrar o estado e qualquer caução"
Escolha o **estado na saída** para que uma devolução posterior possa ser comparada com ele, e registre uma **caução** se algum valor mudou de mãos. A moeda da caução assume por padrão a da coleção.
:::

:::step title="Marque para a proveniência se fizer parte da história"
Marque **incluir na proveniência** para um empréstimo institucional ou uma exposição, e um evento de proveniência correspondente é gerado. Deixe desmarcado para um empréstimo pessoal informal, que permanece apenas no histórico de empréstimos.
:::
::::

### Um único empréstimo em aberto por exemplar

Um exemplar físico só pode estar em um lugar por vez, então um exemplar pode ter no máximo um empréstimo **de saída em aberto**. Se você tentar emprestar um exemplar que já está fora, a seção bloqueia e pede que você devolva o empréstimo atual primeiro. Essa regra vale também na API JSON.

## Devolver um empréstimo

Encerrar um empréstimo é um passo próprio, não uma edição, para que capture o que uma edição não capturaria.

::::steps
:::step title="Abrir o empréstimo e marcá-lo como devolvido"
Abra o empréstimo a partir de qualquer lista e depois escolha **Marcar como devolvido**.
:::

:::step title="Registrar a devolução"
Informe a data em que a peça voltou e o **estado na entrada**. Definir um estado na entrada atualiza o estado atual do exemplar e traz o exemplar de volta à sua custódia.
:::
::::

Quando o estado na entrada é pior que o estado na saída, o empréstimo é sinalizado como possível dano, tanto no próprio empréstimo quanto na lista de riscos **Devolvidos em pior estado**.

## Exportar o que está fora

O botão **Exportar o que está fora** baixa um CSV dos empréstimos em aberto na direção atual, para que você tenha uma lista simples do que está no momento nas mãos de outra pessoa, ou nas suas.

## Relacionado

- Os empréstimos também aparecem no histórico próprio de um exemplar. Consulte @doc(copies.track) para o registro do exemplar ao qual eles se ligam.
