---
id: dataSafety.backupCollectionData
title: Faça backup dos dados da sua coleção
slug: faca-backup-dos-dados-da-sua-colecao
section: seguranca-e-manutencao-dos-dados
---

# Faça backup dos dados da sua coleção

"Como eu tiro meus dados daqui" merece uma resposta direta. Esta página declara claramente o que o KolleK consegue exportar de dentro do aplicativo hoje, o que ainda não consegue, e qual é o caminho real de backup enquanto isso.

## O que você pode exportar hoje

**Definições de tipo de coleção.** Um **@doc(collectionTypes.overview, "tipo de coleção")** pode ser exportado como um arquivo JSON (seu nome, cor, grupos de campos, campos e opções) e importado em qualquer conta do KolleK. Veja @doc(collectionTypes.importExport).

Essa é a lista honesta e completa.

## O que você ainda não pode exportar

Atualmente não existe exportação nativa de itens, exemplares, fotos ou coleções inteiras, nem a importação correspondente. Os dados do seu catálogo ainda não podem ser retirados do aplicativo como um arquivo, pela interface.

:::note
A importação e exportação de itens e coleções estão na lista de capacidades planejadas. A **@doc(troubleshooting.featureStatus, "página de status de recursos")** é o registro mantido de como isso está, então confira lá em vez de supor.
:::

Se você precisa de acesso estruturado aos seus dados hoje, a **@doc(api.overview, "API JSON")** consegue ler tudo na sua conta, o que é um caminho viável para quem tem perfil técnico.

## O caminho real de backup hoje

Se sua instância é autohospedada, o backup confiável é feito no nível da instância: um dump do banco de dados mais um arquivo compactado do volume de armazenamento que guarda fotos e documentos. Isso captura absolutamente tudo, incluindo o que a exportação dentro do aplicativo não alcança. O passo a passo está em @doc(selfHosting.backupAndRestore).

Se outra pessoa hospeda o KolleK para você, ela é quem tem essa capacidade de backup. Pergunte quais são os arranjos de backup dela; é uma pergunta justa e importante.

## Para onde ir agora

- Autohospedando? Configure backups reais em @doc(selfHosting.backupAndRestore).
- Mover uma configuração de tipo entre contas é abordado em @doc(collectionTypes.importExport).
- Veja o que mais está planejado na **@doc(troubleshooting.featureStatus, "página de status de recursos")**.
