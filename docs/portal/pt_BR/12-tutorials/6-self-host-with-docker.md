---
id: tutorials.selfHostWithDocker
title: "Tutorial: Auto-hospede o KolleK com Docker"
slug: auto-hospede-o-kollek-com-docker
section: tutoriais
---

# Tutorial: Auto-hospede o KolleK com Docker

Neste tutorial você vai levar uma máquina sem nada nela até uma instância do KolleK em execução: clonar o projeto, configurar o ambiente, gerar a chave da aplicação, iniciar a stack, criar a primeira conta e conceder o primeiro administrador de instância. No final você vai ter uma instância funcional e saber onde os guias operacionais mais aprofundados continuam.

Vamos acompanhar Alex, que está configurando uma instância para o clube de colecionadores dele em um pequeno servidor doméstico. Os passos são idênticos em um VPS ou em um notebook.

Espere que isso leve de quinze a trinta minutos, a maior parte esperando a primeira construção.

## Antes de começar

Você precisa de:

- Uma máquina com **Docker Engine 24 ou mais recente** e o **plugin Compose** (o comando `docker compose`, não o antigo `docker-compose`).
- **Git**, para clonar o projeto.
- Um terminal e familiaridade básica em executar comandos nele.

Também ajuda dar uma olhada antes na @doc(selfHosting.index, "visão geral da auto-hospedagem"), porque ela apresenta a única regra que este tutorial vai insistir: a chave da aplicação é definida uma vez e nunca alterada.

## Passo 1: Clone o projeto e crie sua configuração

```bash
git clone https://github.com/djaiss/beaver.git
cd beaver
cp .env.docker.example .env
```

O arquivo `.env` é a configuração da sua instância. Tudo que um operador mexe rotineiramente vive nele, e o @doc(selfHosting.configure, "guia de configuração") percorre isso grupo por grupo. Para um primeiro boot, só os dois próximos passos são obrigatórios.

## Passo 2: Gere a chave da aplicação

O KolleK criptografa dados sensíveis em repouso com uma chave que você gera uma única vez:

```bash
docker compose run --rm app php artisan key:generate --show
```

Copie a saída (ela começa com `base64:`) e cole no `.env` como o valor de `APP_KEY`.

:::warning
Defina a chave da aplicação uma vez e nunca a altere em uma instância em produção. Tudo que é criptografado, o que inclui nomes, itens e sessões, se torna permanentemente ilegível sob uma chave diferente. Guarde uma cópia da chave em algum lugar seguro, porque um backup de banco de dados só é restaurável com a chave que o criptografou.
:::

A história completa, incluindo como a rotação deliberada de chave é suportada, está em @doc(selfHosting.applicationKeyAndEncryption).

## Passo 3: Revise as senhas e a URL

Abra o `.env` em um editor e verifique três coisas:

- **`DB_PASSWORD` e `DB_ROOT_PASSWORD`.** Ambas vêm como valores de exemplo. Troque-as por senhas fortes suas antes do primeiro início, porque é no primeiro início que o banco de dados é criado com elas.
- **`APP_URL`.** O endereço que seus usuários vão digitar. Alex define `http://server.local:8000` para a rede do clube. O padrão é `http://localhost:8000`.
- **`APP_PORT`.** A porta publicada, `8000` a menos que você mude.

## Passo 4: Inicie a stack

```bash
docker compose up -d --build
```

A primeira execução constrói a imagem e leva alguns minutos. O Compose então inicia quatro contêineres:

- **app**, o servidor web. É o único papel que executa migrações de banco de dados, então o esquema é montado exatamente uma vez.
- **queue**, o worker em segundo plano que envia e-mails e processa jobs.
- **scheduler**, que executa os jobs de manutenção diários.
- **mysql**, o banco de dados.

Verifique se tudo está de pé com `docker compose ps`. Quando o contêiner app relatar saudável, abra sua `APP_URL` em um navegador. Você deve ver a tela de acesso do KolleK.

## Passo 5: Crie a primeira conta

Vá até a página de cadastro e se registre. Isso funciona exatamente como para qualquer usuário, o passo a passo está em @doc(accounts.create), e faz de você o proprietário da primeira conta da instância.

Alex se registra, chega na checklist de primeiros passos, e resiste a catalogar qualquer coisa até que o trabalho de operador esteja terminado.

## Passo 6: Conceda o primeiro administrador de instância

Um administrador de instância consegue ver todas as contas da instância, a partir do painel de administração da instância. A flag é concedida pela linha de comando:

```bash
docker compose exec app php artisan kollek:make-instance-administrator you@example.com
```

Use o e-mail com o qual você acabou de se registrar. O mesmo comando com `--revoke` retira a flag. O que a flag faz, e propositalmente não faz, está coberto em @doc(instanceAdmin.grantAccess).

## O resultado

Você tem uma instância funcional: o aplicativo web respondendo na sua URL, um worker de fila e um agendador rodando ao lado, dados em um volume de banco de dados nomeado, e você mesmo como proprietário de conta e administrador de instância. Os membros do clube já podem registrar as próprias contas, ou você pode @doc(tutorials.inviteHousehold, "convidar pessoas para a sua").

## Uma coisa a fazer antes de relaxar

De fábrica, a instância só grava o e-mail de saída em um arquivo de log em vez de enviá-lo. Convites, links mágicos e redefinições de senha vão silenciosamente a lugar nenhum até você configurar um serviço de e-mail de verdade. Isso é proposital, e corrigir é um trabalho rápido: @doc(selfHosting.setupEmailDelivery).

## Erros comuns a evitar

- **Perder a chave da aplicação.** Faça o backup dela agora, separado do banco de dados. Sem ela, os backups são texto cifrado.
- **Deixar as senhas de banco de dados de exemplo.** Troque-as antes do primeiro início, não depois.
- **Pular a configuração de e-mail.** O primeiro relato de "meu convite nunca chegou" vai ser sobre isso.

## Para onde ir depois

- Percorra todas as configurações que você pulou em @doc(selfHosting.configure).
- Configure @doc(selfHosting.backupAndRestore, "backups") antes que o catálogo cresça e fique precioso.
- Quando uma nova versão sair, siga @doc(selfHosting.upgrade).
