---
id: dataSafety.howProtected
title: Como seus dados são protegidos
slug: como-seus-dados-sao-protegidos
section: conceitos-fundamentais
---

# Como seus dados são protegidos

Um catálogo registra o que você possui, quanto vale e onde é guardado. Isso é sensível por natureza, e o KolleK trata a informação dessa forma. Esta página explica as proteções em termos simples, e é honesta sobre onde elas terminam.

## Criptografado em repouso

Campos sensíveis (nomes, detalhes de itens, valores e muito mais) são criptografados no banco de dados usando a chave de criptografia da instância. Alguém que obtivesse uma cópia do arquivo do banco de dados sem a chave encontraria as colunas sensíveis ilegíveis.

Isso acontece automaticamente. Não há nada para ativar e nada para configurar como usuário.

## Toda alteração é registrada

O KolleK mantém uma trilha de auditoria das ações dos usuários. Quando Sam edita um item, o registro mostra quem fez isso, o que mudou e quando, e ele alimenta o feed de atividades da conta e o próprio log de cada item. O nome de quem realizou a ação é capturado no momento, então o histórico permanece legível mesmo que o usuário dessa pessoa seja excluído depois. Veja @doc(activity.feedAndAuditTrail).

## O limite honesto

:::note
A criptografia em repouso protege o conteúdo armazenado no banco de dados. Não é criptografia de ponta a ponta. A aplicação consegue ler seus dados para exibi-los a você, e quem opera a instância detém a chave de criptografia.
:::

Na prática, isso significa que sua confiança segue o operador. Se você @doc(selfHosting.index, "hospeda sua própria instância"), esse operador é você, e você guarda a chave no seu próprio hardware. Se alguém hospeda o KolleK para você, tecnicamente essa pessoa detém a chave, exatamente como em qualquer outra aplicação web hospedada.

Duas consequências que vale a pena conhecer:

- **A chave é preciosa.** Se ela for perdida, os dados criptografados não podem ser recuperados por ninguém. Operadores devem ler @doc(selfHosting.applicationKeyAndEncryption).
- **Backups importam.** A criptografia protege contra bisbilhotagem, não contra perda. Quem hospeda sua própria instância deve seguir @doc(selfHosting.backupAndRestore).

## O que você controla

Você escolhe o que sai da conta. Hoje, nada sai: nenhuma coleção é alcançável de fora da sua conta. Cada coleção carrega uma @doc(sharing.overview, "configuração de visibilidade") que registra para quem ela é destinada, e quando o compartilhamento chegar, uma coleção que você marcou como pública se tornará a única superfície que um estranho poderá ver.

## Próximos passos

- Veja quem alterou o quê em @doc(activity.feedAndAuditTrail).
- Reforce seu próprio acesso com @doc(security.index).
- Opera sua própria instância? Leia @doc(selfHosting.applicationKeyAndEncryption).
