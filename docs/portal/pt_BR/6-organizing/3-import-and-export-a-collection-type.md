---
id: collectionTypes.importExport
title: Importe e exporte um tipo de coleção
slug: importar-e-exportar-um-tipo-de-colecao
section: organizacao
---

# Importe e exporte um tipo de coleção

Um @doc(collectionTypes.overview, "tipo de coleção") bem construído vale a pena compartilhar. O KolleK pode exportar a definição de um tipo como um arquivo JSON e importar de volta, para que você possa copiar uma configuração entre contas, compartilhá-la com outro colecionador, ou guardar uma cópia antes de refazê-la.

Você precisa da função de editor ou proprietário.

## O que é levado, e o que não é

A exportação contém apenas a definição do tipo: seu nome, sua cor, seus grupos de campos, seus campos personalizados e as opções de quaisquer campos de seleção.

:::note
Exportar um tipo não exporta os itens nem os dados deles. Atualmente não existe importação ou exportação de itens ou de uma coleção inteira. Veja a @doc(troubleshooting.featureStatus, "página de status de recursos") para saber como isso está e @doc(dataSafety.backupCollectionData) para entender qual portabilidade existe hoje.
:::

## Exporte um tipo

::::steps
:::step title="Abra o tipo"
Nas configurações da conta, abra **Tipos de coleção** e selecione o tipo que você quer exportar.
:::

:::step title="Exporte"
Escolha **Exportar**. O KolleK baixa um arquivo JSON que descreve o tipo.

::screenshot{label="Editor de tipo com a opção de exportar"}
:::
::::

O arquivo é texto simples. Você pode lê-lo, guardá-lo com seus backups ou enviá-lo para alguém.

## Importe um tipo

A importação funciona a partir de JSON colado, então primeiro abra o arquivo que você recebeu em qualquer editor de texto e copie o conteúdo.

::::steps
:::step title="Comece a importação"
Nas configurações da conta, abra **Tipos de coleção** e escolha **Importar**.
:::

:::step title="Cole o JSON"
Cole a definição do tipo no campo e confirme. O KolleK valida e cria o tipo com seus grupos, campos e opções.

::screenshot{label="Formulário de importação com JSON colado"}
:::

:::step title="Confira o resultado"
Abra o novo tipo e verifique se os campos chegaram como esperado, depois vincule-o a uma coleção para começar a usá-lo.
:::
::::

## Um exemplo prático

O amigo de Noah também coleciona vinil e refinou um tipo "Discos de Vinil" com campos agrupados: informações de lançamento (artista, álbum, ano de lançamento) e detalhes de prensagem (prensagem, velocidade, vinil colorido). Em vez de reconstruir tudo à mão, Noah pede a exportação, cola o JSON na sua própria conta e tem a estrutura idêntica em segundos.

Se você quiser ver o formato exato que o importador espera, exporte primeiro qualquer tipo já existente, como o tipo pronto Gibis, e use-o como modelo. Suas próprias exportações sempre são importadas de volta sem problemas.

## Para onde ir depois

- Refine o tipo importado em @doc(collectionTypes.setup).
- Entenda o que mais pode e não pode ser exportado em @doc(dataSafety.backupCollectionData).
