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
