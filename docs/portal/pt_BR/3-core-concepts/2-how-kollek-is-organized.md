---
id: kollek.howOrganized
title: Como o KolleK é organizado
slug: como-o-kollek-e-organizado
section: conceitos-fundamentais
---

# Como o KolleK é organizado

Esta página mostra o mapa completo antes de qualquer detalhe. Tudo o mais nesta seção aprofunda uma parte dele.

## A espinha dorsal: quatro níveis

Tudo o que você cataloga no KolleK vive em um encadeamento simples:

- Uma **@doc(accounts.usersAndRoles, "conta")** é o seu espaço de trabalho. Tudo abaixo dela pertence a exatamente uma conta.
  - Uma **@doc(collections.overview, "coleção")** é um grupo nomeado de coisas, como "Meus quadrinhos" ou "Adega de vinhos".
    - Um **@doc(items.itemsVsCopies, "item")** é um tipo de coisa, como "Amazing Spider-Man #1".
      - Um **@doc(items.itemsVsCopies, "exemplar")** é uma instância física desse item que você realmente possui.

A conta de Emma contém sua coleção "Meus quadrinhos". Dentro dela está o item "Amazing Spider-Man #1". Ela possui duas unidades, então o item tem dois exemplares, cada um com sua própria condição, local de armazenamento e valor.

A divisão entre item e exemplar é o coração do modelo, e tem @doc(items.itemsVsCopies, "sua própria página"). Se você só ler uma página de conceitos, leia essa.

## Os auxiliares compartilhados

Ao redor da espinha dorsal ficam algumas ferramentas de toda a conta. Elas são definidas uma vez e reutilizadas em todo lugar:

- **@doc(collectionTypes.overview)** decidem quais detalhes cada tipo de item registra. Um tipo Quadrinhos pede um número de edição, um tipo Vinho pede uma safra.
- **@doc(organizing.categoriesSetsAndSeries)** agrupam itens de três formas diferentes: organizando dentro de uma coleção, acompanhando uma lista finita até a conclusão e conectando uma franquia entre coleções.
- **@doc(tags.overview)** são rótulos livres compartilhados por toda a conta, como "Assinado".
- **@doc(locations.overview)** descrevem onde os exemplares vivem fisicamente, e se aninham: uma caixa em uma prateleira em um cômodo.
- **@doc(conditions.overview)** classificam o estado de um exemplar, de Novo a Danificado.

## A camada de histórico

Cada exemplar também carrega @doc(copyHistory.concept, "seu próprio histórico"): quanto você pagou, quanto já valeu ao longo do tempo, seguro, empréstimos, manutenção, proveniência e cada lugar onde já foi guardado. O exemplar mostra seu estado atual, e os registros de histórico contam a história por trás dele.

## Mantendo tudo organizado

:::note
Detalhes descritivos vivem no item. Tudo que é físico (condição, local, dinheiro, histórico) vive no exemplar. Na dúvida, pergunte-se "isso é verdade para qualquer exemplar, ou só para este".
:::

## Próximos passos

- Conheça o espaço de trabalho e as pessoas nele em @doc(accounts.usersAndRoles).
- Vá direto à ideia central em @doc(items.itemsVsCopies).
- Prefere fazer a ler? Experimente o @doc(gettingStarted.quickStart, "início rápido de cinco minutos").
