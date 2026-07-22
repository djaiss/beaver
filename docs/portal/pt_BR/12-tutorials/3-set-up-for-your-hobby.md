---
id: tutorials.setupForHobby
title: "Tutorial: Configure sua conta para um hobby específico"
slug: configure-sua-conta-para-um-hobby
section: tutoriais
---

# Tutorial: Configure sua conta para um hobby específico

Adicionar um item é fácil. Adicionar duzentos só é fácil se a conta estiver preparada antes. Neste tutorial você vai adaptar o KolleK a um hobby específico antes de cadastrar dados em massa: moldar o tipo de coleção e seus campos personalizados, construir um mapa de locais que espelhe seu espaço real, e semear um vocabulário de tags, para que cada item que você adicionar depois seja rápido e consistente.

Vamos acompanhar Noah, que está prestes a catalogar cerca de trezentos discos de vinil. A mesma abordagem funciona para qualquer hobby, então substitua pelo seu conforme avança.

Espere que isso leve cerca de meia hora, e que economize muitas horas depois.

## Antes de começar

- Termine @doc(tutorials.catalogueFirstCollection, "Catalogue sua primeira coleção do início ao fim") ou pelo menos o @doc(gettingStarted.quickStart, "início rápido"), para que o ciclo central fique familiar.
- Conheça os conceitos por trás de @doc(collectionTypes.overview, "tipos de coleção e campos personalizados"), @doc(locations.overview, "locais") e @doc(tags.overview, "tags"). Se não conhecer, dê uma folheada nessas páginas.
- Pense um pouco sobre o que você realmente quer registrar para cada item. Dez minutos com um bloco de notas vale mais do que refazer campos depois de cinquenta cadastros.

## Passo 1: Molde o tipo de coleção

Noah começa com o tipo pronto **Discos de Vinil** que veio com sua conta. Ele já registra Minha avaliação, um grupo **Informações de lançamento** (Artista, Álbum, Ano de lançamento) e um grupo **Detalhes da prensagem** (Prensagem/Edição, Velocidade, Cor do vinil).

Isso já está perto do que ele quer, mas ele compra muitas prensagens japonesas e se importa com a condição das capas. Então ele ajusta o tipo.

::::steps
:::step title="Abra o tipo"
Vá até as configurações de tipos de coleção e selecione **Discos de Vinil**. O editor salva conforme você avança, então não há botão de salvar para procurar.

::screenshot{label="Editor de tipo de coleção mostrando os campos de Discos de Vinil"}
:::

:::step title="Adicione os campos que você vai realmente usar"
Noah adiciona um campo de texto **País de prensagem** ao grupo Detalhes da prensagem, e um campo **Condição da capa** como uma seleção com as opções pelas quais ele classifica. Os tipos de campo disponíveis são texto, número, data, sim ou não, seleção e avaliação (até cinco estrelas).
:::

:::step title="Agrupe e ordene os campos"
Crie um novo grupo se um conjunto de campos pertencer junto, e arraste os campos para a ordem que você quer no formulário do item. Os grupos existem só para manter formulários longos legíveis.
:::
::::

Por que isso importa: campos personalizados definidos agora aparecem em todo formulário de item em qualquer coleção que use esse tipo. Decidi-los com antecedência significa trezentos registros consistentes em vez de trezentos improvisados.

:::note
Projete os campos pensando nas perguntas que você vai fazer depois. "Quais discos são vinil colorido" só é respondível se Cor do vinil for um campo. Um detalhe escondido em uma descrição não pode ser filtrado.
:::

## Passo 2: Construa seu mapa de locais

Noah guarda os discos em dois lugares: uma sala de audição com três prateleiras, e caixas no depósito. Ele modela exatamente isso, porque um local no KolleK só é útil se corresponder a um lugar que você pode fisicamente visitar.

::::steps
:::step title="Crie os lugares de nível principal"
Em @doc(locations.setup, "configurações de locais"), crie **Sala de Audição** 🛋️ e **Depósito** 📦. Esses são os cômodos.
:::

:::step title="Aninhe as subdivisões reais"
Dentro de Sala de Audição, crie **Prateleira A**, **Prateleira B** e **Prateleira C**. Dentro de Depósito, crie **Caixa 1** e **Caixa 2**. Locais se aninham na profundidade que você precisar, então uma caixinha dentro de uma caixa dentro de um cômodo funciona bem.
:::
::::

Por que isso importa: todo exemplar aponta para um local, e movimentações futuras são registradas como @doc(copies.move, "histórico de local"). Um bom mapa agora significa que "onde está aquele disco" sempre tem uma resposta exata.

## Passo 3: Semeie seu vocabulário de tags

As tags cruzam coleções e hierarquias, o que as torna ideais para os rótulos que não cabem em nenhum outro lugar. Noah cria seu conjunto inicial em @doc(tags.manageAccount, "configurações de tags"): **Assinado**, **Primeira prensagem**, **Prensagem japonesa**, **Para vender** e **Precisa de limpeza**.

Dois hábitos mantêm as tags úteis:

- Mantenha-as poucas e reutilizáveis. Uma tag usada uma única vez é um fato que deveria estar em um campo ou uma anotação.
- Combine a grafia antes que outras pessoas participem. "Assinado" e "Autografado" como tags separadas vão te assombrar.

Você sempre pode criar uma tag na hora, enquanto edita um item, então esta lista só precisa cobrir os rótulos que você já sabe que quer.

## Passo 4: Importe um tipo em vez de construir um

Existe um atalho que vale conhecer. Um tipo de coleção pode ser @doc(collectionTypes.importExport, "exportado e importado como JSON"). Se um amigo já construiu um ótimo tipo para vinis, ele pode exportá-lo, e você pode importá-lo colando o JSON, trazendo o nome, a cor, os grupos, os campos e as opções de seleção em uma única etapa.

:::note
Importar um tipo traz apenas a definição do tipo. Não importa itens nem seus dados. Ainda não existe importação de itens ou de coleção inteira, e o estado real disso é acompanhado na @doc(troubleshooting.featureStatus, "página de status de recursos").
:::

Noah importa um tipo "Compactos 45 RPM" que um amigo do clube compartilhou, e ele aparece ao lado dos próprios tipos, pronto para ser vinculado a uma coleção.

## Passo 5: Crie a coleção e conecte tudo

Agora as peças se juntam.

::::steps
:::step title="Crie a coleção"
Noah cria uma coleção chamada "Vinil", escolhe o emoji 💿 e escreve uma breve descrição.
:::

:::step title="Ative os tipos que ela precisa"
Ele ativa tanto o tipo **Discos de Vinil** quanto o tipo importado **Compactos 45 RPM**. Uma coleção pode usar vários tipos, e cada item escolhe o que combina com ele.
:::

:::step title="Defina a moeda"
Ele define a moeda da coleção para aquela em que realmente compra discos. Ela pode diferir da moeda padrão da conta, e todo dinheiro nos exemplares desta coleção será exibido nela.
:::
::::

## O resultado

Adicione um disco agora e sinta a diferença: o formulário faz exatamente as perguntas certas, o menu de local oferece prateleiras reais, e as tags que você precisa já existem. A partir daqui, o cadastro em massa é um ritmo, não uma série de decisões.

## Erros comuns a evitar

- **Projetar campos demais.** Dez campos que você preenche vencem vinte e cinco que você pula. Você pode adicionar campos depois; preenchê-los retroativamente é a parte tediosa.
- **Locais que não correspondem à realidade.** Se não existe uma Prateleira B física, o local "Prateleira B" vai ficar desatualizado quase imediatamente.
- **Usar tags para o que campos fazem melhor.** Uma classificação, um ano ou uma avaliação pertence a um campo personalizado, onde pode ser um valor real, não um rótulo.

## Para onde ir depois

- Comece a cadastrar itens com @doc(items.addAndEdit).
- Acompanhe sua peça mais valiosa de verdade em @doc(tutorials.trackValuableItem, "Acompanhe a vida completa de um item valioso").
- Trabalhando com outras pessoas? @doc(tutorials.inviteHousehold, "Convide sua família ou clube").
