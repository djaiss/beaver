---
id: accounts.usersAndRoles
title: Contas, usuários e funções
slug: contas-usuarios-e-funcoes
section: conceitos-fundamentais
---

# Contas, usuários e funções

O KolleK é construído em torno de um espaço de trabalho, a conta, e das pessoas que o compartilham. Esta página explica o limite e o modelo de permissões em linguagem simples, para que nada sobre acesso jamais seja surpresa.

## A conta é o limite

Uma **conta** é um espaço de trabalho privado. Cada coleção, item, exemplar, tipo, tag e local vive dentro de exatamente uma conta. Nada vaza entre contas, e ninguém de fora da sua pode ver o que está dentro, a menos que você @doc(sharing.overview, "compartilhe uma coleção") deliberadamente.

Quando Emma se registrou, o KolleK criou duas coisas ao mesmo tempo: seu usuário pessoal e uma conta nova, da qual ela é proprietária. Se ela convidar seu parceiro Sam, ele entra na conta dela e trabalha no mesmo catálogo.

## Uma pessoa, uma conta

Um **usuário** é uma pessoa autenticada, vinculada a um endereço de e-mail, e um usuário pertence a exatamente uma conta.

:::note
O mesmo e-mail não pode estar em duas contas. Alguém que já tem sua própria conta não pode aceitar um convite para a sua. Se essa pessoa quiser se juntar a você, ela precisaria usar um endereço de e-mail diferente, ou excluir sua própria conta primeiro.
:::

## As três funções

Todo membro de uma conta tem uma função, escolhida no momento em que é convidado e alterável depois por um proprietário:

- Um **visualizador** pode navegar por tudo na conta, mas não pode criar ou alterar nada. Leo, amigo de Emma, é um visualizador: ele pode admirar o catálogo, não editá-lo.
- Um **editor** pode criar e alterar o conteúdo do catálogo: coleções, itens, exemplares, fotos e todos os registros de histórico. Sam é um editor.
- Um **proprietário** pode fazer tudo o que um editor faz, e também administrar a própria conta: convidar e remover membros, alterar funções, gerenciar as configurações da conta e excluir a conta. Emma é a proprietária.

A leitura é aberta a todo membro, incluindo visualizadores. Escrever exige a função de editor ou proprietário. Administrar a conta exige a função de proprietário. A página @doc(collaboration.rolesInPractice, "funções na prática") mapeia isso para tarefas concretas, se você quiser a tabela completa.

Uma conta deve sempre manter pelo menos um proprietário. O KolleK não permite que o último proprietário seja rebaixado ou removido, então uma conta nunca pode ficar sem acesso a si mesma.

## Um sinalizador que não é uma função

Se você ouvir falar em **administrador da instância**, isso é algo completamente diferente. É um sinalizador de nível de servidor para quem opera a própria instalação do KolleK. Ele não concede nada a mais dentro da conta dessa pessoa, e não tem nenhuma relação com visualizador, editor ou proprietário. Isso é abordado em @doc(instanceAdmin.panel, "o painel de administração da instância") para operadores.

## Próximos passos

- Traga alguém para a conta com @doc(collaboration.invitePeople).
- Altere o que um membro pode fazer em @doc(collaboration.manageMembersAndRoles).
- Continue os conceitos com @doc(collections.overview).
