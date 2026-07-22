---
id: webhooks.overview
title: Webhooks
slug: webhooks
section: desenvolvedores-e-a-api
---

# Webhooks

Webhooks permitem que um sistema externo receba uma chamada HTTP do KolleK quando algo acontece na sua conta. Você já pode configurá-los hoje, e esta página mostra como. Mas leia o próximo parágrafo primeiro, porque ele contextualiza tudo o mais.

:::note
Nenhum evento do aplicativo dispara um webhook atualmente. A infraestrutura de registro, assinatura e entrega já está pronta e testada, mas os eventos só vão começar a disparar conforme o domínio de coleções crescer. Configure seu receptor agora se quiser; só não conte com isso para nada ainda. A **@doc(troubleshooting.featureStatus, "página de status de recursos")** acompanha quando isso muda.
:::

## O que existe hoje

Registrar um endpoint armazena uma URL de destino com sua própria chave secreta de assinatura. Quando o KolleK eventualmente disparar eventos, cada um será entregue a todo endpoint ativo que você tiver registrado, assinado para que seu receptor consiga verificar que ele realmente veio da sua instância.

Endpoints de webhook pertencem ao seu usuário, não à conta inteira.

## Registre um endpoint

No aplicativo, abra as configurações do seu perfil e vá para **Webhooks**. Adicione a URL onde seu receptor escuta, com um rótulo para você lembrar para que ela serve. Cada endpoint recebe sua própria chave secreta de assinatura, uma string de 64 caracteres gerada quando o endpoint é criado. Guarde-a junto com seu receptor.

Um operador também pode criar um endpoint pela linha de comando:

```bash
php artisan kollek:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

O comando exibe o id do endpoint e sua chave secreta de assinatura.

## O payload que seu receptor deve esperar

Toda entrega é um `POST` em JSON com este formato:

```json
{
  "event": "example.event",
  "happened_at": "2026-07-20T14:30:00+00:00",
  "data": {}
}
```

- `event` nomeia o que aconteceu. Nenhum nome de evento está definido ainda.
- `happened_at` é um timestamp ISO 8601 de quando aconteceu.
- `data` carrega o payload daquele evento.

## Verificando assinaturas

Toda entrega inclui um cabeçalho `Signature`: um hash HMAC SHA256 do corpo bruto da requisição, calculado com a chave secreta de assinatura do seu endpoint. Recalcule o mesmo hash do seu lado e compare. Se forem diferentes, descarte a requisição, porque ela não veio da sua instância.

```php
$computed = hash_hmac('sha256', $rawRequestBody, $signingSecret);

if (! hash_equals($computed, $request->header('Signature'))) {
    abort(401);
}
```

## Entrega e novas tentativas

As entregas são enfileiradas e enviadas em segundo plano. Uma entrega que falha é tentada novamente até 3 vezes, com espera exponencial crescente. Seu receptor deve responder rapidamente com um status 2xx e fazer o trabalho de verdade de forma assíncrona.

Em uma instância autohospedada, as entregas rodam no worker da fila, então a função de fila precisa estar em execução. Veja @doc(selfHosting.installDocker).

## Para onde ir agora

- Confira o que já está no ar e o que está pendente na **@doc(troubleshooting.featureStatus, "página de status de recursos")**.
- Comece a construir contra a API enquanto isso, começando por @doc(api.authenticate).
