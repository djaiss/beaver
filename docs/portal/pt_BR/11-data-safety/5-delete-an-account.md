---
id: accounts.delete
title: Exclua uma conta
slug: exclua-uma-conta
section: seguranca-e-manutencao-dos-dados
---

# Exclua uma conta

Excluir uma conta é a ação mais destrutiva no KolleK. Ela remove o espaço de trabalho inteiro: toda coleção, todo item, todo exemplar com seu histórico completo, toda foto e documento, e o acesso de todo membro. Só um **@doc(accounts.usersAndRoles, "proprietário")** pode fazer isso.

:::warning
Excluir uma conta não pode ser desfeito. Nada vai para a lixeira, nada pode ser restaurado, e ninguém, nem mesmo quem administra a instância, pode trazer isso de volta. Todo membro perde tudo de uma vez.
:::

## Antes de excluir

Vá com calma e confira três coisas:

- **Isso é realmente o que você quer, em vez de @doc(users.deleteSelf, "excluir apenas seu próprio usuário")?** Sair de uma conta compartilhada exige apenas remover a si mesmo. A conta e o catálogo sobrevivem sem você.
- **Alguém mais depende disso?** Todo membro da conta perde acesso e dados no momento em que você confirma. Avise-os antes.
- **Você já tirou o que precisava dela?** Exporte quaisquer **@doc(collectionTypes.importExport, "definições de tipo de coleção")** que queira manter. Se a instância for autohospedada, faça um backup completo primeiro, como descrito em @doc(selfHosting.backupAndRestore). Depois da exclusão, não sobra nada para fazer backup.

## Exclua a conta

Nas **Configurações da conta**, encontre a opção de exclusão na zona de risco, e confirme. A conta e tudo o que ela contém são removidos, e todos os membros são desconectados definitivamente.

## O que desaparece depois

Tudo. Coleções, itens, exemplares, categorias, sets, séries, tags, locais, tipos e campos personalizados, fotos, documentos, os históricos completos dos exemplares, o registro de atividades, todos os membros, e qualquer convite pendente. Os endereços de e-mail envolvidos ficam livres para cadastrar contas novas, mas essas contas começam vazias.

## Para onde ir agora

- Remover apenas a si mesmo é abordado em @doc(users.deleteSelf).
- Para exclusões recuperáveis, veja @doc(dataSafety.restoreFromTrash).
