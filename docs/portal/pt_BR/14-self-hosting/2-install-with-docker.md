---
id: selfHosting.installDocker
title: Instalar com Docker
slug: instalar-com-docker
section: auto-hospedagem
---

# Instalar com Docker

Este é o guia de instalação oficial. Ele leva você de uma máquina com Docker até uma instância do KolleK em execução, com sua primeira conta criada. Espere que o processo todo leve cerca de quinze minutos.

O arquivo `docker/README.md` do repositório documenta o mesmo procedimento do ponto de vista do operador e é mantido sincronizado com o código. Se esta página e esse arquivo algum dia divergirem, confie no `docker/README.md`.

## Antes de começar

Você precisa de:

- Uma máquina com **Docker Engine 24 ou mais recente** e o **plugin Compose** (`docker compose`).
- Uma cópia do repositório do KolleK, clonada ou baixada.
- Dez minutos de atenção para o arquivo de ambiente. É onde acontecem os erros que realmente importam.

Nada mais. A stack traz seu próprio banco de dados MySQL, e sessões, cache e a fila de jobs são baseados em banco de dados, então não há Redis para instalar.

## Instalar

::::steps
:::step title="Crie seu arquivo de ambiente"
A partir da raiz do repositório, copie o modelo de ambiente do Docker:

```bash
cp .env.docker.example .env
```

Esse arquivo comanda toda a stack. Você vai editá-lo nos dois próximos passos.
:::

:::step title="Gere a chave da aplicação"
Gere uma chave e copie a saída:

```bash
docker compose run --rm app php artisan key:generate --show
```

Cole o valor exibido em `.env` como `APP_KEY`. Essa chave criptografa seus dados em repouso. **Defina-a agora e nunca a altere depois.** Uma chave alterada torna todo campo criptografado e toda sessão permanentemente ilegíveis. Leia @doc(selfHosting.applicationKeyAndEncryption) antes de continuar, caso ainda não tenha lido.
:::

:::step title="Revise as senhas e a URL"
No `.env`, altere `DB_PASSWORD` e `DB_ROOT_PASSWORD` de seus valores de exemplo, e defina `APP_URL` com o endereço que seus usuários vão acessar. O padrão é `http://localhost:8000`, o que funciona bem para um primeiro teste na sua própria máquina.
:::

:::step title="Inicie a stack"
Construa e inicie tudo:

```bash
docker compose up -d --build
```

A primeira construção leva alguns minutos. Quando terminar, o contêiner web aplica as migrações do banco de dados automaticamente e a instância sobe no seu `APP_URL`.
:::

:::step title="Crie sua primeira conta"
Abra a URL em um navegador e use a página de cadastro para se registrar. Isso cria seu usuário pessoal e sua primeira conta, exatamente como descrito em @doc(accounts.create).

::screenshot{label="Página de cadastro de uma instância recém-instalada"}
:::

:::step title="Conceda a si mesmo o acesso de administrador de instância"
Se você quiser o painel de administração de toda a instância, conceda a flag ao seu usuário:

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

Veja @doc(instanceAdmin.grantAccess) para saber o que isso concede, e o que não concede.
:::
::::

## O que está realmente em execução

A stack do Compose inicia quatro contêineres. Três deles rodam a mesma imagem do KolleK em papéis diferentes, escolhidos pela variável de ambiente `CONTAINER_ROLE`:

- **app** serve a aplicação web através de nginx e PHP. É o único contêiner que executa migrações de banco de dados, e faz isso na inicialização.
- **queue** processa jobs em segundo plano (e-mail, entregas, registro de logs) das filas `high`, `default` e `low`.
- **scheduler** dispara os jobs de manutenção diários descritos em @doc(selfHosting.scheduledJobs).

O quarto contêiner é o **mysql**, rodando MySQL 8.4.

Seus dados vivem em dois volumes Docker nomeados, independentes dos contêineres: `db-data` para o banco de dados e `storage-data` para fotos e documentos enviados. Os contêineres podem ser reconstruídos e substituídos livremente; os volumes persistem.

:::note
Os três contêineres da aplicação precisam compartilhar o mesmo `.env`, e acima de tudo a mesma `APP_KEY`. O arquivo Compose já organiza isso. Mantenha essa característica se você personalizar a configuração.
:::

## Se você preferir executar as migrações você mesmo

Por padrão, o contêiner web migra o banco de dados toda vez que é iniciado, o que deixa as atualizações sem intervenção manual. Se você quiser controle manual, defina `RUN_MIGRATIONS=false` no `.env` e execute as migrações você mesmo quando necessário:

```bash
docker compose exec app php artisan migrate --force
```

## Para onde ir depois

- Percorra @doc(selfHosting.configure) para entender o que mais o `.env` controla.
- Faça o e-mail funcionar em @doc(selfHosting.setupEmailDelivery). Até você fazer isso, convites e links de acesso vão para um arquivo de log em vez de uma caixa de entrada.
- Configure @doc(selfHosting.backupAndRestore, "backups") antes de colocar dados reais na instância.
