---
id: selfHosting.upgrade
title: Atualize sua instância
slug: atualize-sua-instancia
section: auto-hospedagem
---

# Atualize sua instância

Atualizar o KolleK foi pensado para ser algo sem graça: buscar a versão mais nova, reconstruir, pronto. Esta página explica por que isso é seguro, e a única etapa pós-atualização que você precisa conhecer.

## Por que atualizações não perdem dados

Duas características tornam o caminho de atualização seguro:

- **Seus dados vivem em volumes nomeados** (`db-data` para o banco de dados, `storage-data` para arquivos), independentes dos contêineres e da imagem. Reconstruir contêineres não os afeta.
- **As migrações são apenas para frente.** O contêiner web aplica as migrações pendentes do banco de dados na inicialização com `migrate --force`, e o KolleK nunca distribui uma migração que redefine ou reescreve dados de forma destrutiva. Uma atualização só adiciona ao seu esquema.

## Atualizar

::::steps
:::step title="Faça backup primeiro"
Faça um dump do banco de dados e um arquivo do armazenamento, conforme descrito em @doc(selfHosting.backupAndRestore). As atualizações são seguras por design, mas um backup transforma "seguro por design" em "seguro, ponto final".
:::

:::step title="Obtenha a nova versão"
No diretório do repositório, busque a versão para a qual você está atualizando:

```bash
git pull
```
:::

:::step title="Reconstrua e reinicie"
```bash
docker compose up -d --build
```

O Compose reconstrói a imagem e recria os contêineres. Na inicialização, o contêiner web aplica automaticamente quaisquer novas migrações, e a instância volta a responder no seu `APP_URL`.
:::
::::

Se você preferir manter as migrações sob controle manual, defina `RUN_MIGRATIONS=false` e execute `docker compose exec app php artisan migrate --force` você mesmo como parte do procedimento, como descrito em @doc(selfHosting.installDocker).

## A etapa do índice de busca de fotos

Uma atualização inclui uma tarefa de manutenção pontual: instâncias anteriores à tela da biblioteca de fotos precisam ter seu índice de busca de fotos construído uma vez, ou a busca de fotos fica vazia para fotos já existentes.

```bash
docker compose exec app php artisan photos:rebuild-search-index
```

O comando é idempotente e seguro para rodar em qualquer instância, então, na dúvida, execute-o. Ele também preenche retroativamente as dimensões de imagem para fotos enviadas antes de as dimensões passarem a ser registradas.

:::note
Não altere `APP_KEY` como parte de uma atualização. A chave sobrevive a todas as versões. Se um guia de atualização em algum momento parecer pedir uma nova chave, você está interpretando errado. Veja @doc(selfHosting.applicationKeyAndEncryption).
:::

## Para onde ir depois

- Mantenha os @doc(selfHosting.backupAndRestore, "backups") em dia para que toda atualização comece a partir de um deles.
- Revise @doc(selfHosting.scheduledJobs), que voltam a rodar automaticamente assim que o contêiner do agendador estiver de volta.
