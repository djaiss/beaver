---
id: api.overview
title: Visão geral da API
slug: visao-geral-da-api
section: desenvolvedores-e-a-api
---

# Visão geral da API

A API do KolleK é uma API JSON que espelha o aplicativo web um a um. Toda capacidade do aplicativo (criar coleções, adicionar itens e exemplares, registrar transações, gerenciar membros) tem um endpoint correspondente, aplicando exatamente as mesmas regras. Se sua função permite fazer algo no navegador, seu token permite fazer isso via HTTP. Se não permite, a API recusa da mesma forma que o aplicativo recusaria.

Esta página te dá o modelo mental. A referência de endpoints completa e sempre atualizada é gerada a partir do código e servida pela sua instância:

- `/docs/api` para a referência navegável.
- `/docs/api.md` para a referência inteira em Markdown.
- `/docs/api/{section}.md` para uma única seção em Markdown, útil para alimentar um tópico específico a uma ferramenta.

:::note
Em uma instância autohospedada, a referência faz parte do site institucional público, que fica desativado por padrão. Um operador a ativa com a configuração `SHOW_MARKETING_SITE`. Veja @doc(selfHosting.configure).
:::

## Restrita à sua conta

A API é isolada por locatário. Um token pertence a um usuário, e um usuário pertence a exatamente uma **@doc(accounts.usersAndRoles, "conta")**, então toda requisição é resolvida através dessa conta. Você não consegue acessar os dados de outra conta, e não passa nenhum identificador de conta em lugar nenhum. Não há nada para configurar: autentique-se, e você está dentro do seu próprio espaço de trabalho.

As mesmas **@doc(accounts.usersAndRoles, "funções")** se aplicam como no aplicativo. O token de um visualizador pode ler, mas não escrever. O token de um editor pode gerenciar o conteúdo do catálogo. Ações restritas ao proprietário (membros, configurações da conta) precisam do token de um proprietário.

## Como os recursos são estruturados

Os recursos se aninham da mesma forma que @doc(kollek.howOrganized, "o KolleK é organizado"):

- Sua **conta** guarda recursos de toda a conta: membros, tipos de coleção, campos personalizados, tags, locais, condições.
- **Coleções** guardam **itens**, junto com categorias e sets.
- **Itens** guardam **fotos** e **exemplares**.
- **Exemplares** carregam os recursos de histórico: transações, avaliações, registros de seguro, empréstimos, registros de manutenção, eventos de procedência, histórico de local, documentos e a linha do tempo combinada.

As respostas seguem livremente o formato do JSON:API: cada recurso volta como `type`, `id`, `attributes` e `links`. Listas são paginadas com um envelope padrão, abordado em @doc(api.rateLimitsAndConventions).

## O que esta seção cobre

Estas páginas cobrem o início e os conceitos que a referência gerada não consegue ensinar: autenticação, convenções e o estado atual dos webhooks. Para qualquer endpoint específico, seus parâmetros e exemplos completos de requisição e resposta, vá direto para `/docs/api`.

:::note
Não existe um modo de teste. Toda requisição à API roda contra sua conta real, então tenha cuidado com chamadas destrutivas enquanto experimenta.
:::

## Para onde ir agora

- Faça sua primeira requisição em @doc(api.authenticate).
- Passe os olhos em @doc(api.rateLimitsAndConventions) antes de escrever um cliente.
- Navegue pela referência gerada em `/docs/api` na sua instância.
