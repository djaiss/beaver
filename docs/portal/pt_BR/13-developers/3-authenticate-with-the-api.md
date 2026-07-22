---
id: api.authenticate
title: Autentique-se com a API
slug: autentique-se-com-a-api
section: desenvolvedores-e-a-api
---

# Autentique-se com a API

Toda requisição à API é autenticada com um token bearer. Esta página te leva do zero até sua primeira requisição bem-sucedida, depois aborda como obter tokens pela própria API e como revogá-los.

Substitua `https://kollek.example.com` nos exemplos pelo endereço da sua instância. A API vive em `/api` nesse endereço.

## O caminho mais rápido: crie uma chave no aplicativo

A forma mais fácil de obter um token é criar uma chave de API a partir do seu perfil.

::::steps
:::step title="Crie uma chave de API"
No aplicativo, abra as configurações do seu perfil e vá para **Chaves de API**. Crie uma chave e dê a ela um rótulo que você vai reconhecer depois, como "Script de relatórios".

::screenshot{label="Configurações de perfil, página de chaves de API com o formulário de nova chave"}
:::

:::step title="Copie o token"
O token é exibido apenas uma vez, logo após a criação. Copie-o agora e guarde em um lugar seguro, como um gerenciador de senhas. Se você perdê-lo, revogue a chave e crie uma nova.
:::

:::step title="Faça sua primeira requisição"
Envie o token no cabeçalho `Authorization`. Uma boa primeira chamada é `/api/me`, que retorna seu próprio usuário:

```bash
curl https://kollek.example.com/api/me \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```
:::
::::

Se você receber de volta um documento JSON descrevendo seu usuário, você está autenticado. Criar e revogar chaves, e ver quando cada uma foi usada pela última vez, é abordado em @doc(apiKeys.manage).

:::note
Tokens não expiram sozinhos. Eles funcionam até você revogá-los, então trate um token como uma senha.
:::

## Obtendo um token pela API

Você também pode se autenticar inteiramente via HTTP, o que combina com scripts e integrações que gerenciam suas próprias credenciais.

Faça login com seu e-mail e senha para receber um token:

```bash
curl -X POST https://kollek.example.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "you@example.com",
    "password": "your-password",
    "device_name": "Reporting script"
  }'
```

A resposta contém seu token em `data.token`. O campo opcional `device_name` nomeia o token para que você possa reconhecê-lo depois na sua lista de chaves.

Duas coisas a saber:

- Se a **@doc(security.twoFactorAuth, "autenticação de dois fatores")** estiver ativada no seu usuário, o endpoint de login também exige um campo `code` contendo um código TOTP atual do seu aplicativo autenticador, ou um dos seus **@doc(security.recoveryCodes, "códigos de recuperação")**.
- Cadastrar-se pela API também funciona: `POST /api/register` cria um usuário com sua própria conta e retorna um token, exatamente como se cadastrar pelo navegador.

Ambos os endpoints são limitados a 6 requisições por minuto, o que é suficiente para logins reais e barra tentativas de força bruta.

## Revogando tokens

Você tem duas opções:

- `DELETE /api/logout` revoga o token que fez a requisição. Use isso quando um script termina com um token temporário.
- A página **Chaves de API** no seu perfil lista todo token e pode revogar qualquer um deles. Os endpoints de chaves de API na referência gerada fazem o mesmo via HTTP.

O KolleK te envia um e-mail quando uma chave é criada ou excluída pelo aplicativo, então uma atividade inesperada de chave não passa despercebida. Veja @doc(security.alertEmails).

## Para onde ir agora

- Aprenda as convenções de requisição em @doc(api.rateLimitsAndConventions).
- Gerencie seus tokens em @doc(apiKeys.manage).
- Explore todo endpoint na referência gerada em `/docs/api`.
