---
id: selfHosting.scheduledJobs
title: Jobs de manutenção agendados
slug: jobs-de-manutencao-agendados
section: auto-hospedagem
---

# Jobs de manutenção agendados

Toda noite, sua instância se organiza sozinha. Esta página explica o que roda, quando, e o que precisa ser verdade para que isso aconteça, para que nada que o aplicativo faz por conta própria jamais surpreenda você.

## Os jobs noturnos

Três jobs rodam diariamente, cada um enfileirado na fila de baixa prioridade:

- **00h30, exclusão de usuários inativos.** Exclui usuários que optaram pessoalmente pela @doc(users.inactiveDeletion, "exclusão automática por inatividade") e ficaram inativos por seis meses ou mais. Cada exclusão é reportada ao endereço em `ACCOUNT_DELETION_NOTIFICATION_EMAIL`. Usuários que nunca ativaram essa opção nunca são afetados.
- **01h00, limpeza da lixeira.** Exclui permanentemente tudo na @doc(dataSafety.restoreFromTrash, "lixeira") mais antigo que o período de retenção (`TRASH_RETENTION_DAYS`, 30 dias por padrão). Dentro da janela, objetos na lixeira permanecem restauráveis.
- **02h00, sinalização de empréstimos vencidos.** Marca @doc(loans.lendAndBorrow, "empréstimos") ativos cuja data de vencimento já passou como vencidos, para que os colecionadores vejam de relance o que ainda não voltou.

Os três são seguros e esperados. Eles só agem sobre coisas que os usuários excluíram explicitamente, ativaram por opção ou definiram com data.

## O que precisa estar rodando

Dois contêineres fazem isso acontecer:

- O papel **scheduler** decide que é a hora e enfileira cada job.
- O papel **queue** de fato os executa.

:::note
Se algum dos contêineres estiver parado, a manutenção para silenciosamente: a lixeira acumula além da data de retenção, empréstimos vencidos permanecem marcados como ativos, e usuários inativos que ativaram a exclusão automática não são limpos. Nada quebra, mas nada roda. Verifique `docker compose ps` se o comportamento noturno parecer ter parado.
:::

Tudo se atualiza na próxima execução bem-sucedida; uma noite perdida não é um problema.

## Para onde ir depois

- Ajuste a janela de retenção em @doc(selfHosting.configure).
- Veja o que os usuários experimentam do outro lado em @doc(dataSafety.restoreFromTrash).
