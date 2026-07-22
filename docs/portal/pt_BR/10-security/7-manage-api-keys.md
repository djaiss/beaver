---
id: apiKeys.manage
title: Gerencie chaves de API
slug: gerencie-chaves-de-api
section: seguranca
---

# Gerencie chaves de API

Uma chave de API é um token pessoal que permite que um script ou aplicativo aja como você através da API do KolleK. Esta página cobre o ciclo de vida: criar uma chave, acompanhá-la e revogá-la. O que você realmente pode fazer com uma chave está na @doc(api.authenticate, "seção de desenvolvedores").

Se você nunca pretende usar a API, pode pular esta página por completo. Nenhuma chave existe até que você crie uma.

## Crie uma chave

::::steps
:::step title="Abra suas configurações de chaves de API"
Vá até seu perfil e abra a área de chaves de API. Você vai ver as chaves que já tem, cada uma com a data do último uso.
:::

:::step title="Nomeie a nova chave"
Escolha criar uma chave e dê a ela um **rótulo** que diga para que ela serve, como "Script de importação" ou "Painel doméstico". Os rótulos são para o seu eu do futuro, na hora de decidir qual chave é seguro revogar.
:::

:::step title="Copie o token imediatamente"
O KolleK mostra o token uma vez, logo após a criação. Copie-o agora e guarde-o em um lugar seguro, como um gerenciador de senhas.

::screenshot{label="Nova chave de API com o token revelado uma vez"}
:::
::::

:::warning
O token é mostrado apenas uma vez. Se você o perder, não poderá vê-lo de novo. Revogue a chave e crie uma nova.
:::

O KolleK te envia um aviso por email sempre que uma chave é criada no seu usuário, então uma chave inesperada nunca passa despercebida.

## Acompanhe suas chaves

A área de chaves de API lista cada chave com seu rótulo e quando foi usada pela última vez. Esse horário de último uso é seu aliado: uma chave que não é usada há meses provavelmente pode ser revogada, e uma chave usada há cinco minutos quando seu script não rodou é uma chave a investigar.

Um hábito mantém isso administrável: uma chave por finalidade. Quando cada integração tem sua própria chave, você pode revogar uma sem quebrar as outras.

## Revogue uma chave

Exclua a chave na mesma lista. Qualquer coisa que ainda esteja usando o token dela para de funcionar imediatamente, e o KolleK te envia um aviso por email da exclusão.

Revogue uma chave quando:

- Você não usa mais o script ou aplicativo ao qual ela pertencia.
- O token pode ter vazado, por exemplo foi enviado a um repositório ou compartilhado em uma conversa.
- Você recebeu um @doc(security.alertEmails, "alerta de chave criada ou excluída") que não reconhece. Nesse caso, troque sua senha também.

:::note
Fazer login pela API também cria um token nos bastidores. Esses tokens de login não disparam o email de chave criada, então os alertas que você recebe continuam significativos.
:::

## Para onde ir depois

- Coloque uma chave em uso com sua primeira requisição: @doc(api.authenticate).
- Entenda os emails relacionados a chaves: @doc(security.alertEmails).
