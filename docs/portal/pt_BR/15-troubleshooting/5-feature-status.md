---
id: troubleshooting.featureStatus
title: Status dos recursos e roteiro
slug: status-dos-recursos-e-roteiro
section: solucao-de-problemas
---

# Status dos recursos e roteiro

O KolleK está em crescimento, e alguns recursos ficam visíveis antes de estarem prontos. Esta página é a lista honesta e única do que já está totalmente disponível hoje e do que ainda está a caminho, para que nenhuma outra página precise ficar em cima do muro. Quando o produto avança, esta página avança junto.

## Disponível agora

Tudo o mais documentado neste portal funciona como descrito, incluindo:

- Coleções, itens, exemplares, fotos, tags, categorias, sets e séries.
- Tipos de coleção com campos personalizados, incluindo importação e exportação de definições de tipo como JSON.
- O histórico completo do exemplar: transações, avaliações, seguro, empréstimos, manutenção, proveniência, histórico de local e documentos, com a linha do tempo unificada.
- Colaboração com as funções de proprietário, editor e visualizador, e convites por e-mail.
- Autenticação de dois fatores, links mágicos, chaves de API e e-mails de alerta de segurança.
- A API JSON completa, com sua referência gerada em `/docs/api`.
- Auto-hospedagem com Docker, dados criptografados em repouso, lixeira com restauração e estatísticas por coleção.

## Ainda não

### Busca global

A caixa de busca no painel é um espaço reservado e ainda não busca nada. O que funciona hoje: filtrar os itens de uma coleção que você tem aberta (veja @doc(collections.chooseView)), e buscar na @doc(photos.library, "biblioteca de fotos").

### Visibilidade e compartilhamento de coleções

Toda coleção carrega uma configuração de visibilidade (privada, compartilhada ou pública), e a configuração é salva, mas ainda não é aplicada. Todo membro de uma conta ainda pode navegar por toda coleção nela, e não existe link público, então uma coleção marcada como pública não é alcançável de fora da conta de forma alguma. Defina a visibilidade agora para registrar sua intenção; ela passa a valer quando o compartilhamento chegar. Veja @doc(sharing.overview).

### Entrega de webhooks

Você pode registrar endpoints de webhook, e cada um recebe um segredo de assinatura, mas nenhum evento do aplicativo ainda dispara um webhook. A infraestrutura de assinatura e entrega já está pronta, esperando os eventos serem conectados. Configure agora se quiser; as entregas chegam conforme o domínio cresce. Veja @doc(webhooks.overview).

### Importação e exportação de itens e coleções

Importação e exportação existem apenas para definições de tipo de coleção. Ainda não há importação ou exportação em nível de item ou de coleção inteira. Para exportar tudo, quem auto-hospeda tem backups completos da instância; veja @doc(dataSafety.backupCollectionData).

### Administração da instância: suporte e avaliações

No painel de administração da instância, as áreas de Suporte e Avaliações são espaços reservados que dizem exatamente isso. O restante do painel funciona; veja @doc(instanceAdmin.panel).

## Como ler esta página

Nada aqui é uma promessa com data. "Ainda não" significa que a base pode já existir, mas você não deve planejar em torno do recurso até que ele passe para a lista acima. Na dúvida, confie nesta página mais do que em qualquer coisa que pareça sugerir o contrário.

Perguntas que esta página não responde provavelmente estão nas @doc(troubleshooting.faq, "perguntas frequentes").
