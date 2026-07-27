---
id: instanceAdmin.panel
title: O painel de administração da instância
slug: painel-de-administracao-da-instancia
section: auto-hospedagem
---

# O painel de administração da instância

O painel de administração da instância, em `/instance-admin`, é onde um @doc(instanceAdmin.grantAccess, "administrador de instância") enxerga todas as contas do servidor: quantas existem, quem está em cada uma, e as poucas ações destrutivas que só um operador deveria ter em mãos. Esta página descreve o que o painel pode fazer e, tão importante quanto, o que ele deliberadamente não pode.

Se você roda uma instância pessoal com apenas uma conta, talvez nunca precise deste painel. Ele mostra seu valor em instâncias compartilhadas, como um servidor de clube ou de família com várias contas.

:::note
O painel só aparece para usuários que carregam a flag de administrador de instância. Qualquer outra pessoa que visite `/instance-admin` recebe uma página não encontrada, não uma de acesso negado, então o painel nunca anuncia sua existência.
:::

## A visão geral

O painel abre em uma visão geral de toda a instância:

- Contagens de **contas**, **usuários**, **coleções** e **itens** em todo o servidor.
- **Contas criadas neste mês** e **usuários ativos neste mês**, para você ver se a instância está crescendo ou parada.
- Um gráfico de **cadastros por mês** nos últimos doze meses.

Esses números são de toda a instância. Eles não revelam o conteúdo do catálogo de ninguém.

## Navegando pelas contas

A área **Contas** lista todas as contas da instância, 25 por página, com a contagem de membros e de coleções de cada conta.

Você pode buscar contas **pelo e-mail de um membro** e filtrar por função. Buscar pelo nome da conta ou da pessoa não é possível, porque os nomes são criptografados no banco de dados e não podem ser comparados ali. O e-mail é o identificador confiável.

Abrir uma conta mostra seus membros, ordenados primeiro por proprietários, depois editores, depois visualizadores, junto com as contagens de coleções e itens da conta e suas quinze entradas mais recentes no log de atividade.

## Opções do site

A área **Opções do site** reúne as configurações do site de marketing público, as páginas que um visitante vê antes de entrar. Esse site vem desligado por padrão em uma instância auto-hospedada (veja @doc(selfHosting.configure)), então, se você nunca o ligou, nada aqui muda o que seus usuários enxergam.

### O banner de anúncio

O banner é a barra preta no topo de cada página do site de marketing. É o lugar para uma frase curta: uma versão que você quer que as pessoas notem, uma janela de manutenção, um evento.

Só a frase é necessária. Todo o resto é opcional:

- **Mostrar o banner** liga e desliga. Coloque em **Não** e nenhuma barra aparece, independentemente do que você tenha preenchido.
- **Versão** é a pequena pílula verde à esquerda, como `v0.9`. Deixe vazia e a pílula some.
- **Link** é o endereço para onde o banner aponta, e **Texto do link** é o que o visitante clica. Deixe o link vazio para um banner que apenas comunica algo.
- **Frase** é o anúncio em si.

O site de marketing é servido em vários idiomas, então a frase e o texto do link são escritos um idioma por vez, com uma aba para cada. Um idioma que você deixar vazio recorre ao inglês, ou seja, preencher só o inglês já garante um banner para todos os visitantes. O ponto verde em uma aba indica que aquele idioma tem a própria frase.

A pré-visualização acima do formulário mostra a barra do jeito que o visitante vai ver, no idioma da aba em que você está. Salvar limpa para você as páginas de marketing em cache, então a mudança fica no ar na hora.

### Limpando o cache de respostas

As páginas de marketing mudam pouco, então cada uma é renderizada uma vez e depois servida a partir de um cache por sete dias. Isso mantém o site público rápido, mas também significa que uma edição pode ficar uma semana sem aparecer.

**Limpar cache** descarta todas as páginas em cache de uma vez. Recorra a isso depois de mudar algo que o site público mostra e que a aplicação desconhece, como uma página de documentação que você editou no servidor. Salvar o banner e moderar um depoimento já limpam o cache por conta própria.

Limpar não perde nada. Cada página é renderizada de novo na próxima vez que alguém a pedir, e o único custo é que o primeiro visitante espera por essa renderização. O mesmo pode ser feito na linha de comando com `php artisan responsecache:clear`, descrito em @doc(selfHosting.cliCommands).

## As ações destrutivas

Três ações no painel alteram ou removem dados, e nenhuma delas pode ser desfeita:

- **Excluir uma conta**, que remove a conta com todas as suas coleções, itens, exemplares, membros e todo o histórico dela.
- **Excluir um usuário**, que remove essa pessoa da conta dela.
- **Alternar a flag de administrador de outro usuário**, que concede ou revoga a administração de instância para outra pessoa.

:::warning
Excluir uma conta ou um usuário por este painel é imediato e permanente. Nada passa pela lixeira, e não há restauração. Verifique duas vezes se você tem a conta ou a pessoa certa antes de confirmar.
:::

Duas salvaguardas protegem a própria instância: um administrador não pode revogar a própria flag, nem excluir o próprio usuário pelo painel. Da forma como for usado, a instância mantém pelo menos um administrador funcional.

## O que o painel não é

O painel é exclusivamente web, por design. A API JSON é restrita a uma única conta, e uma superfície que abrange toda a instância não tem lugar nela, então nenhuma dessas capacidades existe como endpoint de API.

As áreas **Support** e **Reviews** visíveis no painel são espaços reservados e ainda não foram construídas. Veja @doc(troubleshooting.featureStatus).

## Para onde ir depois

- Conceda ou revogue a própria flag em @doc(instanceAdmin.grantAccess).
- Entenda o que os proprietários de conta já podem fazer sem você em @doc(collaboration.manageMembersAndRoles).
- Revise as outras ferramentas de operador em @doc(selfHosting.cliCommands).
