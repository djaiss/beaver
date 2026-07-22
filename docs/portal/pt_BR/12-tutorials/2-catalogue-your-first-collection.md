---
id: tutorials.catalogueFirstCollection
title: "Tutorial: Catalogue sua primeira coleção do início ao fim"
slug: catalogue-sua-primeira-colecao
section: tutoriais
---

# Tutorial: Catalogue sua primeira coleção do início ao fim

Neste tutorial você vai levar uma conta totalmente nova até uma coleção real e populada. Você vai criar uma coleção, ver os campos personalizados que ela registra, adicionar um item com uma foto de capa, registrar o exemplar físico que você possui, anotar quanto pagou por ele, adicionar uma primeira avaliação e ler as estatísticas resultantes.

Vamos acompanhar Emma, que coleciona quadrinhos. Ela catalogou um item rapidamente no @doc(gettingStarted.quickStart, "início rápido de cinco minutos"). Desta vez ela faz tudo direito, e ao final seu catálogo sabe quanto seu gibi custou, quanto ele vale e onde ele está guardado.

Espere que isso leve de vinte a trinta minutos.

## Antes de começar

- Você precisa de uma conta na qual consiga entrar. Se não tiver uma, @doc(accounts.create, "crie sua conta") primeiro.
- Você deve saber a diferença entre um item e um exemplar. Se não tiver certeza, leia agora @doc(items.itemsVsCopies, "Itens e exemplares"). O tutorial se apoia nisso o tempo todo.
- Tenha à mão uma coisa real que você possua para catalogar, de preferência com uma foto e uma lembrança aproximada do que pagou por ela.

## Passo 1: Crie a coleção

Todo item vive dentro de uma @doc(collections.overview, "coleção"), então é por aí que tudo começa.

::::steps
:::step title="Comece uma nova coleção"
No seu painel, escolha **Nova coleção**.

::screenshot{label="Painel com o botão Nova coleção"}
:::

:::step title="Dê um nome e um rosto a ela"
Emma dá o nome "Meus Quadrinhos", escolhe o emoji 📚 e escreve uma descrição de uma linha. O emoji e a descrição são opcionais, mas facilitam identificar a coleção depois.
:::

:::step title="Escolha o tipo de coleção"
Ative o tipo pronto **Quadrinhos** para esta coleção. O tipo é o que decide quais campos personalizados os itens desta coleção podem registrar.
:::

:::step title="Deixe a visibilidade e a moeda como estão por enquanto"
Uma coleção nova é **privada** por padrão, o que significa que só você pode vê-la, e usa a moeda padrão da sua conta. Ambos podem ser alterados depois. Salve a coleção.
:::
::::

Por que isso importa: as escolhas neste formulário moldam tudo que vem depois. O tipo controla os campos que você preenche para cada item, e a moeda controla como o dinheiro nos exemplares desta coleção é exibido.

## Passo 2: Veja o que o tipo Quadrinhos registra

Sua conta veio com uma dúzia de @doc(collectionTypes.overview, "tipos de coleção") prontos. Antes de adicionar itens, vale a pena ver o que o tipo Quadrinhos vai pedir a você, para que nada no formulário do item seja surpresa.

Abra as configurações de tipos de coleção e selecione **Quadrinhos**. Você vai encontrar:

- **Minha avaliação**, um campo de classificação por estrelas.
- Um grupo **Informações de publicação**: Edição # (um número), Editora (uma escolha entre Marvel, DC, Image, Dark Horse ou Independente), Roteirista, Artista e Data da capa.
- Um grupo **Condição e classificação**: Variante e Assinado, ambos perguntas de sim ou não.

Você não precisa mudar nada. Se quiser adicionar ou reordenar campos, o @doc(collectionTypes.setup, "guia de configuração de tipos") cobre isso. Para este tutorial, os valores padrão são exatamente o que Emma precisa.

## Passo 3: Adicione o item com seus detalhes e foto

Agora a parte gostosa. Abra sua nova coleção.

::::steps
:::step title="Crie o item"
Escolha adicionar um **Novo item** e dê a ele um **nome**. Emma digita "Amazing Spider-Man #300".
:::

:::step title="Preencha os campos personalizados"
Como a coleção usa o tipo Quadrinhos, o formulário oferece os campos que você acabou de revisar. Emma define **Edição #** como 300, **Editora** como Marvel, e responde **Assinado** com não. Preencha o que souber e pule o resto. Campos vazios não têm problema.

::screenshot{label="Formulário de item mostrando os campos personalizados de Quadrinhos"}
:::

:::step title="Envie uma foto de capa"
Adicione uma **foto** do item. Arquivos JPEG, PNG, WebP e GIF de até 10 MB são aceitos. Se você adicionar várias, marque a melhor como a foto principal. Ela se torna a capa que você vai reconhecer em toda lista.
:::
::::

Por que isso importa: detalhes descritivos como número da edição e editora pertencem ao item, porque são verdadeiros para todo exemplar daquele quadrinho no mundo. Nada do que você digitou até agora diz algo sobre o exemplar físico nas mãos da Emma. É o que vem a seguir.

## Passo 4: Registre o exemplar que você possui

Um item sem um @doc(items.itemsVsCopies, "exemplar") é só uma entrada em uma enciclopédia. O exemplar é a coisa física que você possui.

::::steps
:::step title="Adicione um exemplar ao item"
No item, adicione um **exemplar**.
:::

:::step title="Classifique e guarde"
Defina sua **condição**. Emma escolhe **Usado** na lista pronta (Novo, Como novo, Usado, Desgastado e Danificado vêm com toda conta). Depois defina seu **local**. Emma mantém o dela em **Depósito**, um dos locais padrão, embora você possa @doc(locations.setup, "construir seu próprio mapa de locais") a qualquer momento.
:::

:::step title="Verifique o status"
Deixe o status como **Possuído**. Os outros status (Encomendado, Emprestado, Vendido, e assim por diante) existem para exemplares que não estão na sua prateleira agora. Salve.
:::
::::

:::note
Se você tem dois exemplares do mesmo gibi, não crie um segundo item. Adicione um segundo exemplar a este. Cada exemplar carrega sua própria condição, local, dinheiro e histórico.
:::

## Passo 5: Registre quanto você pagou

Aqui é onde o KolleK vai além de uma lista. O dinheiro nunca vive no item ou em uma anotação. Ele vive em uma **transação** no exemplar, para que seus registros permaneçam precisos à medida que crescem. A explicação completa está em @doc(copies.recordPaymentsAndValue).

::::steps
:::step title="Abra o histórico do exemplar"
Abra a aba **Histórico** do item. Ela mostra um exemplar de cada vez, e você só tem um até agora.
:::

:::step title="Adicione uma transação de compra"
Adicione uma **transação** do tipo **Compra**. Emma informa o valor que pagou, a loja como contraparte, e a data da compra. Impostos, taxas, frete e uma referência estão disponíveis se você precisar.

::screenshot{label="Formulário de nova transação com o tipo Compra selecionado"}
:::
::::

Por que isso importa: a transação de compra é o que dá ao exemplar seu **preço pago** e sua **data de aquisição**. As estatísticas que você vai ver no passo 7 usam essa data para mostrar como sua coleção cresceu.

## Passo 6: Adicione uma primeira avaliação

Quanto você pagou e quanto vale são fatos diferentes, e o KolleK os mantém separados de propósito. O valor é registrado como uma **avaliação**.

Ainda no histórico do exemplar, adicione uma **avaliação**. Emma escolhe o tipo **Minha estimativa**, informa quanto acredita que o gibi valeria hoje, e define a confiança como **Média**. Quando um dia ela fizer uma avaliação profissional, vai adicionar uma nova avaliação em vez de editar esta, e a estimativa antiga permanecerá como histórico.

:::note
Um preço de compra é uma transação, nunca uma avaliação. O valor estimado do exemplar sempre vem da avaliação mais recente, e o preço pago vem da sua primeira transação de aquisição.
:::

## Passo 7: Veja o que você construiu

Abra a coleção. Você deve ver:

- Seu item com a foto de capa, na visualização em grade.
- Uma contagem de um item, e um valor total correspondente à sua avaliação.

Agora abra as **estatísticas** da coleção. Mesmo com um único item, já há algo a ler: o valor total estimado, o valor por local, e a aquisição caindo no mês em que você comprou. O @doc(insights.collectionStatistics, "guia de estatísticas") explica de onde vem cada número.

## O que você conquistou

Você exercitou o ciclo central completo do KolleK: uma coleção com um tipo, um item com detalhes e uma foto, um exemplar com condição e local, uma transação carregando o dinheiro e uma avaliação carregando o valor. Todo recurso do produto se constrói sobre os registros que você acabou de criar.

## Erros comuns a evitar

- **Criar itens duplicados em vez de exemplares.** Dois exemplares do mesmo gibi são um item com dois exemplares.
- **Digitar o preço de compra como uma avaliação.** O preço que você pagou é uma transação de Compra. Avaliações são para o que vale agora.
- **Colocar detalhes do exemplar no item.** Condição, local e dinheiro sempre pertencem a um exemplar, porque um segundo exemplar vai diferir nos três.

## Para onde ir depois

- Adapte a conta ao seu hobby de verdade em @doc(tutorials.setupForHobby, "Configure sua conta para um hobby específico").
- Aprofunde-se em uma peça valiosa em @doc(tutorials.trackValuableItem, "Acompanhe a vida completa de um item valioso").
- Catalogando com família ou amigos? Veja @doc(tutorials.inviteHousehold, "Convide sua família ou clube").
