---
id: selfHosting.backupAndRestore
title: Faça backup e restaure sua instância
slug: backup-e-restauracao
section: auto-hospedagem
---

# Faça backup e restaure sua instância

Não existe backup automático dentro do KolleK. Proteger os dados é tarefa do operador, e esta página é o procedimento. Hoje ela também é a resposta real para "como eu exporto tudo", como @doc(dataSafety.backupCollectionData) explica do lado do colecionador.

## O que é um backup completo

Três coisas, e todas as três importam:

1. **O banco de dados**, no volume `db-data`. Todo registro: contas, coleções, itens, exemplares, histórico.
2. **O volume de armazenamento**, `storage-data`. Toda foto e documento enviados.
3. **A chave da aplicação**, `APP_KEY` do seu `.env` (mais `APP_PREVIOUS_KEYS`, se definida).

:::warning
Um backup sem a chave da aplicação correspondente não é um backup. Campos criptografados são restaurados como texto cifrado ilegível sem a chave que os gravou. Guarde a chave junto, ou ao lado, de cada backup que você fizer. Veja @doc(selfHosting.applicationKeyAndEncryption).
:::

## Fazer backup

Faça o dump do banco de dados:

```bash
docker compose exec mysql mysqldump -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" > kollek-backup.sql
```

Arquive o volume de armazenamento:

```bash
docker run --rm -v kollek_storage-data:/data -v "$PWD":/backup alpine tar czf /backup/kollek-storage.tar.gz -C /data .
```

Copie os dois arquivos, e uma cópia do seu `.env`, para fora do servidor. Automatize isso com um cron job noturno e mantenha mais de uma geração; um backup do qual você nunca restaurou é uma esperança, não um plano.

## Restaurar

Em uma máquina nova, restaure nesta ordem:

1. Instale a mesma versão do KolleK seguindo @doc(selfHosting.installDocker), mas defina `APP_KEY` (e `APP_PREVIOUS_KEYS`) a partir do seu backup, em vez de gerar uma chave nova.
2. Inicie a stack uma vez para que os volumes existam, depois carregue o dump do banco de dados:

```bash
docker compose exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" < kollek-backup.sql
```

3. Descompacte o arquivo de armazenamento no volume de armazenamento:

```bash
docker run --rm -v kollek_storage-data:/data -v "$PWD":/backup alpine tar xzf /backup/kollek-storage.tar.gz -C /data
```

4. Reinicie a stack com `docker compose up -d` e acesse para verificar.

## O comando que apaga tudo

:::warning
`docker compose down -v` remove os volumes nomeados, ou seja, o banco de dados e todo arquivo enviado. Nunca use a flag `-v` em uma instância em produção. O `docker compose down` simples é seguro e mantém os volumes intactos.
:::

## Para onde ir depois

- Entenda o que a chave protege em @doc(selfHosting.applicationKeyAndEncryption).
- Veja o que os colecionadores conseguem exportar de dentro do aplicativo em @doc(dataSafety.backupCollectionData).
