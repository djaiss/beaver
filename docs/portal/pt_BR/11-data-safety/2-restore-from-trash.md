---
id: dataSafety.restoreFromTrash
title: Restaure algo da lixeira
slug: restaure-algo-da-lixeira
section: seguranca-e-manutencao-dos-dados
---

# Restaure algo da lixeira

A maioria das exclusões do dia a dia no KolleK não é definitiva. Coleções, itens, exemplares, categorias e sets vão primeiro para a lixeira, onde esperam antes de serem removidos para sempre. Esta página explica o que vai parar lá, por quanto tempo fica, e como trazer algo de volta.

Você precisa ter a função de editor ou proprietário para restaurar ou excluir permanentemente.

## O que vai para a lixeira, e o que não vai

Cinco tipos de objetos passam por exclusão reversível para a lixeira:

- **@doc(collections.manage, "Coleções")**, junto com o que elas contêm
- **@doc(items.addAndEdit, "Itens")**
- **@doc(copies.track, "Exemplares")**
- **@doc(categories.organizeItems, "Categorias")**
- **@doc(sets.trackCompletion, "Sets")**

:::note
Fotos, documentos e os registros de histórico de um exemplar (transações, avaliações, empréstimos e o resto) não vão para a lixeira. Excluir um deles o remove imediata e permanentemente.
:::

## Por quanto tempo as coisas são mantidas

Objetos na lixeira são mantidos por um período de retenção, 30 dias, a menos que quem administra sua instância tenha configurado um período diferente. Uma limpeza diária remove permanentemente tudo o que passou do prazo. Cada entrada na lixeira mostra quantos dias ainda restam, e a lista é ordenada com os mais urgentes primeiro, então o que está prestes a desaparecer fica no topo.

## Restaure algo

::::steps
:::step title="Abra a lixeira"
Vá para a **Lixeira** a partir da sua conta. Você pode pesquisar se a lista estiver longa.

::screenshot{label="Lista da lixeira com os dias restantes por entrada"}
:::

:::step title="Encontre a entrada"
Cada entrada mostra o que é, quando foi excluída, e quem a excluiu.
:::

:::step title="Restaure"
Escolha **Restaurar**. O objeto volta exatamente para onde estava, com seus dados intactos.
:::
::::

Se você excluiu uma coleção por engano, restaurá-la também traz de volta o que ela continha. Restaure os pais antes de procurar pelos filhos.

## Esvazie a lixeira

Você também pode excluir permanentemente tudo o que está na lixeira de uma vez, sem esperar o período de retenção acabar.

:::warning
Esvaziar a lixeira é permanente. Tudo o que está nela é removido para sempre, e nada pode ser recuperado depois disso.
:::

## Para onde ir agora

- Excluindo a si mesmo em vez dos seus dados? Veja @doc(users.deleteSelf).
- Autohospedando e quer redes de segurança de verdade? Veja @doc(selfHosting.backupAndRestore).
