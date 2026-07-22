---
id: troubleshooting.faq
title: Perguntas frequentes
slug: perguntas-frequentes
section: solucao-de-problemas
---

# Perguntas frequentes

Respostas curtas para as perguntas que aparecem repetidamente. Cada uma remete à página que cobre o assunto em detalhes.

## Qual é a diferença entre um item e um exemplar?

Um item é o tipo de coisa, como "Amazing Spider-Man #1". Um exemplar é uma instância física que você realmente possui. Possui três unidades do mesmo quadrinho? Isso é um item com três exemplares, cada um carregando sua própria condição, local, valor e histórico. Essa é a ideia mais importante do KolleK. Veja @doc(items.itemsVsCopies).

## Posso pertencer a mais de uma conta?

Não. Um usuário pertence a exatamente uma conta, e um endereço de e-mail só pode ter um usuário. Isso também significa que um convite para a conta de outra pessoa não pode ser aceito por um e-mail que já tem sua própria conta. Veja @doc(accounts.usersAndRoles).

## O KolleK é realmente gratuito?

Sim. Não existe cobrança dentro do aplicativo de forma alguma: sem planos, sem níveis, sem recursos bloqueados por pagamento. Auto-hospedar é gratuito, e todo recurso está incluído, não importa como você o execute. Veja @doc(kollek.hostingOptions).

## Como faço para exportar meus dados?

Hoje, de dentro do aplicativo, você pode exportar @doc(collectionTypes.importExport, "definições de tipo de coleção como JSON"). Ainda não há exportação de item ou de coleção inteira. A resposta completa para quem auto-hospeda é um backup no nível da instância do banco de dados e dos arquivos enviados, abordado em @doc(selfHosting.backupAndRestore). O resumo honesto está em @doc(dataSafety.backupCollectionData).

## Por que não consigo remover ou rebaixar o último proprietário?

Uma conta deve sempre manter pelo menos um proprietário, caso contrário ninguém poderia administrá-la, convidar membros ou excluí-la. Promova outra pessoa a proprietário primeiro. Veja @doc(collaboration.manageMembersAndRoles).

## Onde está o recurso de busca?

Buscar em tudo a partir do painel ainda não está disponível; a caixa que você vê ali é um espaço reservado. O que funciona hoje: filtrar dentro de uma coleção que você tem aberta, e buscar na sua biblioteca de fotos. Veja @doc(troubleshooting.featureStatus).

## Os webhooks já funcionam?

Parcialmente. Você pode registrar endpoints e cada um recebe um segredo de assinatura, mas nenhum evento do aplicativo ainda dispara um webhook. A infraestrutura de entrega está pronta; os eventos chegam conforme o produto cresce. Veja @doc(webhooks.overview).

## Meus dados são criptografados, e o que isso protege?

Campos sensíveis são criptografados em repouso no banco de dados com a chave da sua instância. Isso protege o conteúdo do banco de dados caso o banco de dados sozinho seja roubado. Não é criptografia de ponta a ponta: quem opera a instância detém a chave e pode acessar os dados. Veja @doc(dataSafety.howProtected).

## Posso adicionar minhas próprias condições?

Sim. Abra **Condições de item** nas configurações da conta para adicionar, renomear ou excluir condições, incluindo as já cadastradas (Novo, Como novo, Usado, Desgastado, Danificado). Veja @doc(conditions.manage).

## Algo foi excluído. Consigo recuperar?

Se foi uma coleção, item, exemplar, categoria ou set, ele foi para a lixeira e pode ser restaurado por 30 dias, por padrão. Fotos, documentos e registros de histórico são removidos imediatamente e não podem ser recuperados de dentro do aplicativo. Veja @doc(dataSafety.restoreFromTrash).

## Ainda com dúvidas?

- Problemas de acesso: @doc(troubleshooting.signIn).
- E-mails que não chegam: @doc(troubleshooting.emailDelivery).
- O que está pronto e o que não está: @doc(troubleshooting.featureStatus).
