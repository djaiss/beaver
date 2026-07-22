---
id: reference.glossary
title: Glossário
slug: glossario
section: referencia
---

# Glossário

Todo termo do produto em um só lugar. Cada entrada tem um link para a página que explica a ideia completamente. Os termos estão listados na ordem em que você os encontra no produto, do espaço de trabalho até os registros de um único exemplar.

## O espaço de trabalho

**Conta.** Seu espaço de trabalho privado, e o limite ao redor de tudo o que você cria. Toda coleção, item e configuração vive dentro de exatamente uma conta. Veja @doc(accounts.usersAndRoles).

**Usuário.** Uma pessoa que faz login. Um usuário pertence a exatamente uma conta e não pode entrar em uma segunda com o mesmo e-mail. Veja @doc(accounts.usersAndRoles).

**Função.** O que um usuário tem permissão para fazer na sua conta: um visualizador lê, um editor cataloga, um proprietário também gerencia a conta. Veja @doc(collaboration.rolesInPractice, "Entendendo as três funções na prática").

## O catálogo

**Coleção.** Um grupo de nível superior que você nomeia, como "Meus Quadrinhos" ou "Adega de Vinhos". Coleções guardam itens e têm sua própria moeda e visibilidade. Veja @doc(collections.overview).

**Tipo de coleção.** Um tipo de coisa que você coleciona (Quadrinhos, Discos de Vinil, Vinhos) que decide quais campos personalizados seus itens registram. Os tipos são compartilhados por toda a conta. Veja @doc(collectionTypes.overview).

**Campo personalizado.** Um detalhe que você define em um tipo de coleção, como "Número da edição" ou "Safra". Seu valor é registrado em cada item. Veja @doc(collectionTypes.overview).

**Grupo de campos.** Uma seção nomeada, como "Informações de publicação", que mantém uma lista longa de campos personalizados legível no formulário do item. Veja @doc(collectionTypes.setup).

**Item.** O tipo de coisa que você cataloga, como "Amazing Spider-Man #1". Os detalhes descritivos vivem no item; as coisas físicas que você possui são seus exemplares. Veja @doc(items.itemsVsCopies).

**Exemplar.** Uma instância física de um item que você realmente possui. Cada exemplar tem sua própria condição, local, valor e histórico. Veja @doc(items.itemsVsCopies).

## Agrupando e encontrando

**Categoria.** Uma ferramenta de organização dentro de uma coleção. Categorias podem se aninhar, como Marvel dentro de Quadrinhos. Veja @doc(organizing.categoriesSetsAndSeries).

**Set.** Uma lista finita que você está tentando completar dentro de uma coleção, acompanhada em relação a uma quantidade alvo. Veja @doc(organizing.categoriesSetsAndSeries).

**Série.** Uma franquia que pode abranger várias coleções, como Harry Potter entre livros e filmes. Uma série não acompanha conclusão. Veja @doc(organizing.categoriesSetsAndSeries).

**Tag.** Uma etiqueta livre compartilhada por todas as coleções da conta, como "Autografado". Um item pode carregar várias tags. Veja @doc(tags.overview).

**Local.** Onde um exemplar vive fisicamente. Locais se aninham para representar espaços reais, como uma caixa em uma prateleira em um cômodo. Veja @doc(locations.overview).

**Condição.** Uma classificação que descreve o estado de um exemplar, como Novo ou Danificado. Veja @doc(conditions.overview).

## O histórico de um exemplar

**Transação.** Um evento financeiro ou de propriedade em um exemplar, como uma compra ou venda. Todo dinheiro vive nas transações. Veja @doc(copies.recordPaymentsAndValue).

**Avaliação.** Quanto um exemplar valia em um determinado momento. O valor estimado atual de um exemplar é sua avaliação mais recente. Veja @doc(copies.recordPaymentsAndValue).

**Registro de seguro.** Cobertura registrada para um exemplar: seguradora, valor segurado, detalhes da apólice e status. Veja @doc(copies.insure).

**Empréstimo.** Um registro de custódia para um exemplar que você emprestou ou pegou emprestado, com suas datas, a parte envolvida e os detalhes de devolução. Veja @doc(loans.lendAndBorrow).

**Registro de manutenção.** Cuidado ou reparo realizado em um exemplar, como limpeza ou restauração. Veja @doc(copies.recordMaintenance).

**Evento de procedência.** Um capítulo na história de propriedade e autenticidade de um exemplar, como uma aquisição, exposição ou avaliação pericial. Veja @doc(copies.traceProvenance).

**Histórico de local.** O registro datado de onde um exemplar viveu ao longo do tempo. Mover um exemplar encerra um registro e abre o próximo. Veja @doc(copies.move).

**Documento.** Um arquivo ou link externo guardado junto a um exemplar ou a um de seus registros, como um recibo em uma transação. Veja @doc(copies.attachDocuments).

## Acesso e segurança

**Visibilidade.** Uma configuração de coleção que registra para quem ela é destinada: privada (só você), compartilhada (todos na conta), ou pública (qualquer pessoa com o link, somente leitura). Registrada hoje, aplicada quando o compartilhamento chegar. Veja @doc(sharing.overview).

**Lixeira.** Onde coleções, itens, exemplares, categorias e sets excluídos aguardam antes de serem descartados definitivamente, e de onde podem ser restaurados. Veja @doc(dataSafety.restoreFromTrash).

**Administrador da instância.** Um sinalizador em nível de servidor, separado das funções de conta, que desbloqueia o painel de administração para quem administra a instância. Veja @doc(instanceAdmin.grantAccess).

**Chave de API.** Um token pessoal que permite que um script ou aplicativo chame a API do KolleK em seu nome. Veja @doc(apiKeys.manage).

**Webhook.** Uma URL que você registra para receber notificações assinadas do KolleK. Nenhum evento do aplicativo dispara uma ainda. Veja @doc(webhooks.overview).

## Para onde ir agora

- Todas as opções que esses termos podem assumir: @doc(reference.fieldAndStatus).
- Os conceitos por trás dos termos, explicados adequadamente: @doc(coreConcepts.index).
