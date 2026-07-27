---
id: selfHosting.cliCommands
title: Administre pela linha de comando
slug: administre-pela-linha-de-comando
section: auto-hospedagem
---

# Administre pela linha de comando

Algumas tarefas de operador vivem na linha de comando, e não no aplicativo web. Esta página lista os comandos artisan que você pode realmente precisar ao rodar uma instância, com um link para a página mais completa de cada um.

Em uma instalação Docker, execute todo comando através do contêiner web:

```
docker compose exec app php artisan <command>
```

## Operação do dia a dia

### Conceder ou revogar administração de instância

```
php artisan kollek:make-instance-administrator you@example.com
php artisan kollek:make-instance-administrator you@example.com --revoke
```

Concede (ou retira) a flag de administrador global do servidor para o usuário com esse e-mail. É assim que o primeiro administrador é inicializado depois da instalação. Veja @doc(instanceAdmin.grantAccess).

### Criar um endpoint de webhook

```
php artisan kollek:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

Registra um endpoint de webhook para um usuário e exibe seu ID e segredo de assinatura. Os usuários também podem fazer isso sozinhos, pelas configurações do perfil. Note que nenhum evento da aplicação ainda dispara webhooks; veja @doc(webhooks.overview).

### Reconstruir o índice de busca de fotos

```
php artisan photos:rebuild-search-index
```

Reconstrói o índice de busca por trás da biblioteca de fotos e preenche retroativamente as dimensões de imagem que estejam faltando. Execute uma vez depois de atualizar para uma versão que introduz a tela de fotos. É seguro executar novamente a qualquer momento; ele pula fotos cujos arquivos estão faltando e não altera mais nada. Veja @doc(selfHosting.upgrade).

### Reconstruir o índice de busca da conta

```
php artisan search:rebuild-index
```

Reconstrói o índice por trás da busca de toda a conta: itens, coleções, exemplares, fotos, empréstimos, locais, conjuntos, séries, categorias, etiquetas e documentos. Execute uma vez depois de atualizar para uma versão que introduz a busca: a migração cria a tabela, mas só este comando a preenche. Passe `--type=item` para reconstruir apenas um tipo de registro. É seguro executar novamente a qualquer momento, e também é o conserto se o índice sair de sincronia. Veja @doc(search.overview).

### Purgar o cache do Cloudflare

```
php artisan kollek:purge-cloudflare-cache
```

Descarta todas as páginas públicas que o Cloudflare guarda, de outro modo servidas do cache dele por sete dias. Nada se perde: cada página é renderizada de novo na próxima visita. O comando precisa de `CLOUDFLARE_API_TOKEN` e `CLOUDFLARE_ZONE_ID`, e avisa quando eles faltam. O mesmo botão existe no painel, descrito em @doc(instanceAdmin.panel).

### Preparar uma localidade para tradução

```
php artisan kollek:localize fr_FR
```

Extrai toda string traduzível da aplicação e a sincroniza com o arquivo JSON da localidade em `lang/`. Veja @doc(selfHosting.addLanguage).

## Apenas para desenvolvimento

Mais dois comandos existem no código, e nenhum deles pertence a uma instância em produção. `kollek:bruno` reinicia o banco de dados com dados de exemplo para testes de clientes de API, o que destruiria dados reais, e `kollek:sync-skills` mantém as ferramentas internas do próprio projeto. Você pode ignorar os dois como operador.

:::warning
Nunca execute `kollek:bruno` em uma instância em produção. Ele apaga o banco de dados e o repovoa com dados de demonstração.
:::

## Para onde ir depois

- Inicialize seu administrador em @doc(instanceAdmin.grantAccess).
- Mantenha a instância atualizada com @doc(selfHosting.upgrade).
- Traduza a interface em @doc(selfHosting.addLanguage).
