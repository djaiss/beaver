---
id: collaboration.manageMembersAndRoles
title: Gerencie membros e funções
slug: gerencie-membros-e-funcoes
section: colaboracao
---

# Gerencie membros e funções

O envolvimento das pessoas muda com o tempo. Um visualizador começa a ajudar com a entrada de dados e precisa virar editor. Alguém sai do clube e deve perder o acesso. Esta página cobre como alterar a função de um membro e removê-lo, e a única proteção que evita que sua conta fique sem ninguém para administrá-la.

Você precisa ser um **@doc(accounts.usersAndRoles, "proprietário")** para tudo nesta página. A lista de membros e os convites pendentes só ficam visíveis para proprietários.

## Veja quem está na sua conta

Abra a área de membros nas configurações da sua conta. Você vai ver:

- **Membros**, cada um com nome, e-mail e função atual.
- **Convites pendentes** que já foram enviados, mas ainda não aceitos, para você saber quem ainda está a caminho. Convites expiram após sete dias.

## Altere a função de um membro

::::steps
:::step title="Encontre o membro"
Na lista de membros, localize a pessoa cujo acesso você quer alterar.
:::

:::step title="Escolha a nova função"
Altere a **função** dela para visualizador, editor ou proprietário. A mudança tem efeito imediato, não há e-mail de confirmação nem etapa de aceite.

::screenshot{label="Linha do membro com o seletor de função aberto"}
:::
::::

Quando a função do Sam muda de visualizador para editor, ele pode começar a adicionar e editar itens assim que a mudança é salva.

:::note
Uma conta precisa manter sempre pelo menos um proprietário. O KolleK vai recusar rebaixar o último proprietário, então você não corre o risco de deixar a conta sem ninguém que consiga administrá-la. Promova outra pessoa a proprietário primeiro, se quiser deixar de sê-lo.
:::

## Remova um membro

Remover um membro tira todo o acesso dele.

:::warning
Remover um membro exclui o usuário dele. O acesso é perdido imediatamente, e isso não pode ser desfeito nesta tela. Se essa pessoa deve voltar mais tarde, você vai precisar convidá-la de novo e ela vai começar do zero.
:::

As contribuições passadas dela, porém, não desaparecem. O **@doc(activity.feedAndAuditTrail, "registro de atividades")** mantém o histórico do que ela fez, porque cada entrada guarda o nome da pessoa no momento em que foi registrada.

A mesma proteção se aplica aqui como nas funções: o último proprietário não pode ser removido.

## Para onde ir agora

- Compare o que cada função permite em @doc(collaboration.rolesInPractice).
- Traga alguém novo com @doc(collaboration.invitePeople).
- Se você está encerrando a conta inteira em vez disso, leia @doc(accounts.delete).
