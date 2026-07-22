---
id: collections.manage
title: Crie e gerencie coleções
slug: crie-e-gerencie-colecoes
section: recursos-principais
---

# Crie e gerencie coleções

Uma @doc(collections.overview, "coleção") é o contêiner onde tudo o mais vive, então geralmente é a primeira coisa que você cria. Esta página cobre como criar uma, cada opção do formulário, como editá-la depois e o que realmente acontece quando você exclui uma.

## Quem pode fazer isso

Criar, editar e excluir coleções exige a função de **editor** ou **proprietário** (@doc(accounts.usersAndRoles, "função")). Visualizadores podem navegar pelas coleções, mas não podem criá-las ou alterá-las.

## Crie uma coleção

Noah está começando um catálogo para seu vinil. Veja o que ele faz.

::::steps
:::step title="Comece uma nova coleção"
Na tela de coleções, escolha **Nova coleção**.

::screenshot{label="Tela de coleções, botão Nova coleção"}
:::

:::step title="Dê um nome e uma descrição"
Dê um **nome**, como "Discos de Vinil", e opcionalmente uma **descrição** curta e um **emoji** para que se destaque nas listas.
:::

:::step title="Escolha os tipos de coleção"
Escolha quais @doc(collectionTypes.overview, "tipos de coleção") se aplicam. Noah escolhe o tipo pronto **Discos de Vinil**, para que seus itens ganhem campos como Artista, Álbum e Prensagem. Você pode habilitar vários tipos, ou nenhum, e mudar isso depois.
:::

:::step title="Defina a moeda e a visibilidade"
Escolha a **moeda** para os valores desta coleção, e sua **visibilidade**. Se estiver em dúvida, mantenha os padrões. Privada é o ponto de partida mais seguro.

::screenshot{label="Formulário da coleção, campos de moeda e visibilidade"}
:::

:::step title="Salve"
Salve a coleção. Ela aparece na sua lista, vazia e pronta para o primeiro item.
:::
::::

## Cada campo, explicado

- **Nome.** Como a coleção aparece em todo lugar. Obrigatório.
- **Descrição.** Uma frase sobre o que ela guarda. Opcional, mas útil quando você tem muitas coleções.
- **Emoji.** Um marcador visual escolhido de uma paleta fixa de doze (📦 📚 💿 🃏 🍷 🎮 🧸 🪙 🖼️ ⌚ 👟 📷). Opcional.
- **Tipos de coleção.** Os tipos que você habilita decidem quais campos personalizados os itens desta coleção podem registrar. Você pode habilitar mais de um, por exemplo Gibis e Livros em uma única coleção "Leitura".
- **Moeda.** Todo valor em dinheiro nesta coleção (valores, estatísticas) usa essa moeda. Dezoito moedas estão disponíveis. Ela pode ser diferente da moeda padrão da sua conta, o que é útil se, por exemplo, você compra seu vinho em euros mas tudo o mais em dólares.
- **Visibilidade.** Para quem a coleção é destinada: **privada** (só você), **compartilhada** (todos na sua conta), ou **pública** (qualquer pessoa com o link, apenas leitura). A configuração é registrada hoje e será aplicada quando o compartilhamento estiver disponível. A página de conceitos sobre @doc(sharing.overview, "visibilidade e compartilhamento") explica o modelo e seu status atual, e @doc(collections.share) mostra como alterá-la.

## Edite uma coleção

Abra a coleção e escolha editá-la. O mesmo formulário aparece com os mesmos campos, e você pode alterar qualquer um deles a qualquer momento. Renomear uma coleção ou trocar seu emoji não afeta nada dentro dela.

## Exclua uma coleção

Abra a coleção, escolha excluí-la e confirme.

:::warning
Excluir uma coleção também envia todos os itens dentro dela para a lixeira. A coleção e seus itens ficam na lixeira por um tempo limitado (30 dias por padrão), e depois são removidos permanentemente.
:::

Enquanto estiver na lixeira você ainda pode mudar de ideia. Veja @doc(dataSafety.restoreFromTrash).

## Para onde ir depois

- Coloque algo nela: @doc(items.addAndEdit).
- Escolha o layout que combina com ela: @doc(collections.chooseView).
- Mostre-a para alguém: @doc(collections.share).
