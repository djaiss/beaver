---
id: reference.fieldAndStatus
title: Referência de campos e status
slug: referencia-de-campos-e-status
section: referencia
---

# Referência de campos e status

Todo conjunto de opções que você encontra em um formulário do KolleK, em um só lugar fácil de escanear. Cada grupo tem um link para o guia que o utiliza. Para definições dos termos em si, veja o @doc(reference.glossary, "glossário").

## Status de exemplar

Definido em cada exemplar que você registra. Usado em @doc(copies.track).

| Status | Significado |
| --- | --- |
| Possuído | Você está com este exemplar. O padrão para um exemplar novo. |
| Encomendado | Comprado ou reservado, a caminho de você. |
| Emprestado | Está com outra pessoa por enquanto. A custódia mudou, a propriedade não. |
| Vendido | Você o vendeu e não é mais o proprietário. |
| Doado | Você o deu de presente. |
| Perdido | Você não consegue encontrá-lo e não espera encontrar. |
| Roubado | Foi tirado de você. |
| Descartado | Descartado ou reciclado, com uma data de descarte opcional. |
| Outro | Qualquer coisa que a lista acima não cobre. |

:::note
Possuído, Encomendado e Emprestado contam como ainda em posse. Um exemplar emprestado ainda é seu, ele só está em outro lugar.
:::

## Tipos de transação

Definido em cada transação. Usado em @doc(copies.recordPaymentsAndValue). Tipos marcados como de aquisição trazem um exemplar para as suas mãos, e a transação de aquisição mais antiga fornece a data de aquisição do exemplar.

| Tipo | Significado |
| --- | --- |
| Compra | Você comprou o exemplar. Aquisição. |
| Venda | Você vendeu o exemplar. |
| Troca | Você trocou algo por ele. Aquisição. |
| Presente recebido | Alguém deu para você. Aquisição. |
| Presente dado | Você deu para alguém. |
| Herança | Ele passou para você. Aquisição. |
| Reembolso | Dinheiro devolvido em uma transação anterior. |
| Taxa | Um custo em torno do exemplar, como uma taxa de leilão. |
| Imposto | Um imposto pago sobre o exemplar. |
| Envio | Um custo de entrega registrado separadamente. |
| Outro | Qualquer evento financeiro que a lista não cobre. |

## Tipos de avaliação e confiança

Definido em cada avaliação. Usado em @doc(copies.recordPaymentsAndValue).

| Tipo de avaliação | Significado |
| --- | --- |
| Estimativa própria | Seu próprio julgamento do valor. |
| Avaliação profissional | Uma avaliação formal feita por um profissional. |
| Estimativa de mercado | Derivada de dados atuais de mercado ou de vendas. |
| Valor de seguro | O valor usado para fins de seguro. |
| Estimativa de leilão | Uma estimativa dada por uma casa de leilões. |
| Estimativa automatizada | Produzida por um serviço ou ferramenta de precificação. |
| Outro | Qualquer outra base para o valor. |

| Confiança | Significado |
| --- | --- |
| Baixa | Um palpite aproximado. |
| Média | Razoavelmente fundamentada. |
| Alta | Bem sustentada, como uma avaliação profissional recente. |
| Desconhecida | A confiança não foi registrada. |

## Status de seguro

Definido em cada registro de seguro. Usado em @doc(copies.insure). O tipo de cobertura em um registro de seguro é texto livre, então não tem uma lista de opções fixa.

| Status | Significado |
| --- | --- |
| Ativo | A apólice cobre atualmente o exemplar. |
| Expirado | O período de cobertura terminou. |
| Cancelado | A apólice foi cancelada antes da data de término. |
| Pendente | A cobertura está organizada, mas ainda não está em vigor. |

## Direções e status de empréstimo

Definido em cada empréstimo. Usado em @doc(loans.lendAndBorrow).

| Direção | Significado |
| --- | --- |
| Emprestado a alguém | Seu exemplar saiu das suas mãos, por exemplo para um amigo ou uma exposição. |
| Emprestado de alguém | A peça de outra pessoa está nas suas mãos. |

| Status | Significado |
| --- | --- |
| Planejado | Combinado, mas ainda não entregue. |
| Ativo | O exemplar está atualmente fora (ou dentro). |
| Atrasado | Ainda fora, além da data prevista. O KolleK sinaliza isso automaticamente todo dia. |
| Devolvido | O empréstimo terminou e o exemplar voltou. |
| Cancelado | O empréstimo nunca aconteceu. |
| Perdido | O exemplar não voltou. |

## Tipos de manutenção

Definido em cada registro de manutenção. Usado em @doc(copies.recordMaintenance).

| Tipo | Significado |
| --- | --- |
| Limpeza | Limpeza de rotina. |
| Reparo | Corrigindo um dano. |
| Revisão | Manutenção periódica, como a revisão de um relógio. |
| Conservação | Trabalho para estabilizar e preservar. |
| Restauração | Trabalho para devolver o exemplar a um estado anterior. |
| Substituição | Substituindo uma peça ou componente. |
| Inspeção | Uma verificação sem intervenção. |

## Tipos de evento de procedência e precisão de data

Definido em cada evento de procedência. Usado em @doc(copies.traceProvenance).

| Tipo de evento | Significado |
| --- | --- |
| Aquisição | O exemplar entrou em uma coleção. |
| Venda | O exemplar foi vendido. |
| Presente | O exemplar mudou de mãos como presente. |
| Herança | O exemplar passou por um espólio. |
| Transferência de propriedade | A propriedade mudou de outra forma. |
| Transferência de custódia | O exemplar se moveu sem mudar de proprietário. |
| Empréstimo | O exemplar saiu em empréstimo. |
| Devolução | O exemplar voltou de um empréstimo. |
| Exposição | O exemplar foi exibido publicamente. |
| Autenticação | O exemplar foi verificado como genuíno. |
| Avaliação pericial | O exemplar foi formalmente avaliado. |
| Restauração significativa | Trabalho de grande porte que faz parte da história. |
| Origem | Onde e quando o exemplar foi feito. |
| Descoberta | O exemplar foi encontrado ou redescoberto. |
| Outro | Qualquer outro capítulo da história. |

As datas de procedência costumam ser incertas, então cada evento carrega uma precisão:

| Precisão | Significado |
| --- | --- |
| Data exata | A data completa é conhecida. |
| Mês | Conhecida até o mês. |
| Ano | Conhecida até o ano. |
| Aproximada | Uma melhor estimativa. Leia como "por volta de". |
| Desconhecida | Nenhuma data foi registrada. |

## Tipos de documento

Definido em cada documento. Usado em @doc(copies.attachDocuments).

| Tipo | Significado |
| --- | --- |
| Recibo | Comprovante de uma compra. |
| Nota fiscal | Uma cobrança pelo exemplar ou por trabalho realizado nele. |
| Certificado | Um certificado que acompanhava o exemplar. |
| Avaliação | Uma avaliação por escrito. |
| Seguro | Documentação da apólice. |
| Fotografia | Uma foto guardada como registro, não como imagem de galeria. |
| Laudo de condição | Uma avaliação por escrito da condição. |
| Laudo de restauração | Um registro do trabalho de restauração. |
| Catálogo | Uma entrada ou listagem de catálogo. |
| Correspondência | Cartas ou e-mails sobre o exemplar. |
| Registro de propriedade | Documentação que comprova a propriedade. |
| Registro de autenticidade | Documentação que comprova que o exemplar é genuíno. |
| Outro | Qualquer outra coisa que valha a pena guardar. |

## Tipos de campo personalizado

Escolhido ao definir um campo personalizado em um tipo de coleção. Usado em @doc(collectionTypes.setup).

| Tipo de campo | Significado |
| --- | --- |
| Texto | Texto livre, como um autor ou uma editora. |
| Número | Um valor numérico, como um número de edição. |
| Data | Uma data de calendário, como uma data de lançamento. |
| Sim / Não | Uma caixa de seleção, como "Autografado". |
| Seleção | Uma escolha dentre uma lista de opções que você define. |
| Avaliação por estrelas | Uma avaliação por estrelas, até cinco estrelas. |

## Visibilidade de coleção

Definido em cada coleção. Usado em @doc(collections.share). A configuração é registrada hoje e será aplicada quando o compartilhamento chegar; veja @doc(troubleshooting.featureStatus).

| Visibilidade | Significado |
| --- | --- |
| Privada | Destinada apenas a você. |
| Compartilhada | Destinada a todos na sua conta. |
| Pública | Destinada a qualquer pessoa com o link, somente leitura, sem precisar entrar. |

## Para onde ir agora

- O que os termos significam: @doc(reference.glossary).
- Os registros onde essas opções vivem: @doc(copyHistory.concept, "O histórico de um exemplar explicado").
