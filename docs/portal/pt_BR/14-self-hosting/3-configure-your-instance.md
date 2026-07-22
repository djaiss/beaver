---
id: selfHosting.configure
title: Configure sua instância
slug: configure-sua-instancia
section: auto-hospedagem
---

# Configure sua instância

Tudo na sua instância é configurado através do arquivo `.env` que você criou durante a @doc(selfHosting.installDocker, "instalação"). Esta página percorre as configurações que um operador realmente mexe, agrupadas pelo que fazem, em vez de listar cada variável que o modelo contém.

Depois de alterar o `.env`, aplique a mudança recriando os contêineres:

```bash
docker compose up -d
```

## Identidade e URL

- `APP_NAME` é o nome exibido na interface e nos e-mails. O padrão é `Kollek`.
- `APP_URL` é o endereço público da sua instância. Os links nos e-mails são construídos a partir dele, então precisa ser o endereço que seus usuários realmente usam.
- `APP_PORT` é a porta do host que o contêiner web publica, `8000` por padrão.

## A chave da aplicação

`APP_KEY` criptografa dados sensíveis em repouso. Você a define uma vez durante a instalação e nunca a altera de forma casual. Ela é importante o suficiente para ter @doc(selfHosting.applicationKeyAndEncryption, "sua própria página"), que também cobre o mecanismo de rotação `APP_PREVIOUS_KEYS`.

## Banco de dados

`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` e `DB_ROOT_PASSWORD` configuram o contêiner MySQL incluído. Altere as duas senhas de seus valores de exemplo antes do primeiro boot. `RUN_MIGRATIONS` controla se o contêiner web migra o banco na inicialização (`true` por padrão).

## E-mail

`MAIL_MAILER` decide como o e-mail sai da sua instância, e o padrão é `log`.

:::note
Com o mailer padrão `log`, nenhum e-mail chega a ser enviado. Convites, links mágicos, redefinições de senha e alertas de segurança são gravados no log da aplicação em vez disso. Configurar um mailer de verdade é a única peça de configuração que praticamente toda instância precisa. Veja @doc(selfHosting.setupEmailDelivery).
:::

## Armazenamento de arquivos

`FILESYSTEM_DISK` é `local` por padrão: fotos e documentos enviados são armazenados no volume `storage-data`. Para usar armazenamento de objetos compatível com S3, defina como `s3` e preencha as variáveis `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` e, para provedores fora da AWS, `AWS_ENDPOINT`. Os arquivos são servidos aos usuários por rotas privadas e verificadas pela conta de qualquer forma, nunca como URLs públicas.

## Manutenção de rotina

- `TRASH_RETENTION_DAYS` é por quanto tempo objetos excluídos de forma reversível ficam na @doc(dataSafety.restoreFromTrash, "lixeira") antes que a limpeza noturna os remova definitivamente. O padrão é 30 dias.
- `ACCOUNT_DELETION_NOTIFICATION_EMAIL` é o endereço notificado quando um usuário exclui sua própria conta de usuário ou é removido pela @doc(users.inactiveDeletion, "limpeza por inatividade"). Aponte-o para você mesmo para que saídas não passem despercebidas.

## O site institucional público

`SHOW_MARKETING_SITE` é `false` por padrão, o que significa que sua instância serve apenas a aplicação em si. Defina como `true` para também servir as páginas públicas institucionais e a referência de API gerada em `/docs/api`. A maioria das instâncias privadas mantém isso desligado; ative se seus desenvolvedores quiserem a referência de API disponível localmente.

## O que você não precisa configurar

Sessões (`SESSION_DRIVER`), cache (`CACHE_STORE`) e a fila de jobs (`QUEUE_CONNECTION`) são todos baseados em `database` por padrão. Os valores padrão estão corretos para a stack fornecida, e não há Redis ou outro serviço para adicionar. Deixe-os como estão, a menos que você saiba exatamente por que está mudando.

## Para onde ir depois

- Faça o e-mail de verdade funcionar em @doc(selfHosting.setupEmailDelivery).
- Entenda a chave que você precisa proteger em @doc(selfHosting.applicationKeyAndEncryption).
- Coloque @doc(selfHosting.backupAndRestore, "backups") em prática.
