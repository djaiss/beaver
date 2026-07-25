---
id: search.overview
title: Busque em toda a sua conta
slug: busque-em-toda-a-sua-conta
section: recursos-principais
---

# Busque em toda a sua conta

Emma coleciona quadrinhos, vinis e cards. Ela tem três coleções, algumas centenas de itens e o dobro de exemplares. Alguém pergunta se ela ainda tem aquela edição do Homem-Aranha com a capa preta. Ela não deveria precisar lembrar em qual coleção ela está.

A busca responde a isso a partir de um único campo. Digite uma palavra e o KolleK olha de uma vez para tudo o que a sua conta guarda: itens, coleções, exemplares, fotos, empréstimos, locais, conjuntos, séries, categorias, etiquetas e documentos.

Abra em **Buscar**, no topo da barra lateral, ou pressione **⌘K** (**Ctrl K** no Windows e no Linux) de qualquer tela.

## O que é buscado

A busca não olha só para os nomes. Ela também olha para as palavras arquivadas em volta de um registro, e é isso que a torna útil em vez de apenas literal.

Buscar `spider` encontra:

- um item chamado **Amazing Spider-Man #300**
- o exemplar **ASM-300-B**, porque o item dele se chama assim
- uma foto chamada `spider-man-300-front.jpg`
- um item que você nunca chamou de "Spider-Man", porque você deu a ele a etiqueta **spider-man**
- um empréstimo a um amigo, porque o exemplar emprestado é esse quadrinho

Assim, uma busca que parte de uma etiqueta, de uma categoria ou de um local ainda leva você ao que você procurava de verdade.

:::note
Tudo o que está na lixeira fica de fora da busca. @doc(dataSafety.restoreFromTrash, "Restaure") e volta a ser localizável.
:::

## Como a correspondência funciona

Quatro regras cobrem tudo.

**Cada palavra precisa corresponder.** Acrescentar uma palavra estreita o resultado em vez de ampliá-lo. `miles davis` encontra só os registros em que as duas palavras aparecem em algum lugar. Se voltar coisa demais, acrescente uma palavra.

**Uma palavra corresponde a partir do começo.** Digitar `spi` encontra **Spider-Man**. Você nunca precisa terminar uma palavra, mas precisa começá-la: buscar `man` não encontra **Spider-Man**, porque `man` não é o começo de nenhuma palavra dele.

**Maiúsculas e pontuação são ignoradas.** `asm-300`, `asm 300` e `ASM_300` se comportam da mesma forma, o que importa quando os seus próprios identificadores usam hifens, pontos ou sublinhados e você não lembra qual.

**Letras isoladas são ignoradas.** Uma letra sozinha é comum demais para ser indexada, então ela é descartada da sua busca. Se você buscar uma única letra e nada mais, recebe nada em vez de tudo.

## Ler os resultados

Os resultados são agrupados pelo que são, com os itens primeiro. Cada linha mostra o nome, um selo dizendo de que tipo de registro se trata, a coleção a que pertence quando há uma, e uma linha de contexto: quantos exemplares um item tem, onde um exemplar está guardado, para quem foi um empréstimo.

À direita de cada linha, **Corresponde ao nome** quer dizer que todas as palavras digitadas apareceram no nome do próprio registro. **Corresponde ao texto** quer dizer que pelo menos uma palavra foi encontrada mais adiante, por exemplo numa descrição ou numa etiqueta. As correspondências de nome vêm primeiro, então a resposta mais próxima costuma ser a primeira linha.

As pastilhas acima dos resultados permitem limitar a um único tipo de registro. Cada uma tem o próprio endereço, então uma busca só de itens pode ser salva nos favoritos ou compartilhada com um colega da mesma conta.

No máximo 50 resultados são mostrados. Quando há mais, a contagem abaixo da lista diz quantos corresponderam no total, e acrescentar uma palavra à busca é o jeito mais rápido de chegar ao que você quer.

## Quem pode buscar o quê

A busca fica limitada à sua conta. Você nunca vê nada de outra conta, e ninguém de fora vê a sua.

Dentro da sua conta, todos os papéis podem buscar, e cada resultado abre uma tela que aquele papel tem permissão de abrir. Os @doc(accounts.usersAndRoles, "leitores") são a exceção em um ponto: etiquetas são gerenciadas numa tela que só proprietários e editores têm, então um leitor não recebe resultados de etiqueta isolados. Ele ainda encontra tudo o que está *etiquetado* como `spider-man`, porque nomes de etiqueta contam para os itens que as carregam.

## Se algo estiver faltando

A busca lê um índice mantido em dia conforme você trabalha, então um item novo é localizável no momento em que você salva.

Dois casos podem deixá-lo brevemente atrasado:

**Você renomeou algo que outros registros mencionam.** Renomear uma coleção obriga a reindexar cada item dela com o nome novo. Isso acontece em segundo plano, então dê um instante.

**Você acabou de atualizar uma instância auto-hospedada.** O índice começa vazio numa instalação existente e precisa ser construído uma vez. Rode @doc(selfHosting.cliCommands, "o comando de reconstrução") e tudo passa a ser localizável:

```bash
php artisan search:rebuild-index
```

Esse comando pode ser rodado de novo a qualquer momento sem risco, então também é o conserto se o índice um dia sair de sincronia.

## Para onde ir agora

- @doc(items.tagAndFind) trata das etiquetas, que é o que faz a busca encontrar coisas que você não nomeou literalmente.
- @doc(organizing.categoriesSetsAndSeries) explica as categorias, conjuntos e séries que também alimentam a busca.
- @doc(photos.library) tem uma busca própria, para quando você está olhando fotos em vez de toda a conta.
