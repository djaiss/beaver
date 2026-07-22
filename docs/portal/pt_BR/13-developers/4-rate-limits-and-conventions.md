---
id: api.rateLimitsAndConventions
title: Limites de taxa e convenções
slug: limites-de-taxa-e-convencoes
section: desenvolvedores-e-a-api
---

# Limites de taxa e convenções

Um punhado de convenções se aplica a toda a API. Aprendê-las uma vez evita surpresas em cada endpoint, então elas vivem aqui em vez de serem repetidas ao longo da referência.

## Limites de taxa

- Requisições autenticadas são limitadas a **60 por minuto** por usuário.
- `POST /api/register` e `POST /api/login` são limitados a **6 por minuto**, o que protege contra ataques de credential stuffing.

Quando você excede um limite, a API responde com HTTP 429. Reduza o ritmo e tente novamente depois de um momento. Se você está escrevendo uma importação em massa, distribua suas requisições em vez de dispará-las o mais rápido possível, e lembre-se de que a API trabalha com um objeto por requisição, já que não há endpoints em massa.

## Paginação

Endpoints de listagem são paginados e compartilham um único envelope:

- `data` guarda a página de recursos.
- `links` guarda as URLs `first`, `last`, `prev` e `next`.
- `meta` guarda a página atual, a contagem total e detalhes relacionados.

As páginas trazem **10 recursos por padrão**. Peça mais com o parâmetro de consulta `per_page`, até um **máximo de 100**. Siga `links.next` até que seja `null` para percorrer uma lista inteira.

## Dinheiro fica na menor unidade da moeda

Todo valor na API (valores estimados, valores de transação, depósitos, valores segurados) é um número inteiro na menor unidade da sua moeda. Para dólares e euros, isso significa centavos: uma compra de $49,99 trafega como `4999`. Isso evita completamente arredondamentos de ponto flutuante. Converta para exibição no seu próprio código, e lembre-se de que cada **@doc(collections.overview, "coleção")** tem sua própria moeda.

## Recusa se comporta como não encontrado

A API aplica as mesmas **@doc(accounts.usersAndRoles, "funções")** do aplicativo web, com uma diferença deliberada: uma ação que você não tem permissão para realizar, ou um recurso em outra conta, responde **404 Not Found**, não 403 Forbidden. Quem faz a chamada não consegue diferenciar "isso não existe" de "isso não é seu", então a API nunca confirma o que existe fora da sua conta.

:::note
Se um endpoint retorna 404 inesperadamente em um objeto que você consegue ver no aplicativo, verifique a função do usuário dono do token que você está usando. O token de um visualizador recebe 404 em toda escrita.
:::

## Erros e validação

Falhas de validação respondem com HTTP 422, com um campo `message` e um objeto `errors` indexado pelo nome do campo. Outros erros seguem a semântica HTTP comum: 401 quando o token está ausente ou revogado, 404 como descrito acima, 429 para limites de taxa.

## Para onde ir agora

- Veja essas convenções aplicadas em endpoints reais na referência gerada em `/docs/api`.
- Pronto para a entrega de eventos algum dia? Leia em que ponto @doc(webhooks.overview) está hoje.
