---
id: tutorials.inviteHousehold
title: "Tutorial: Convide sua família ou clube e defina permissões"
slug: convide-sua-familia-ou-clube
section: tutoriais
---

# Tutorial: Convide sua família ou clube e defina permissões

Uma conta do KolleK é um espaço de trabalho compartilhado, e trazer pessoas para dentro dela com segurança é, em grande parte, uma questão de escolher a função certa para cada pessoa. Neste tutorial você vai convidar duas pessoas em funções diferentes, ver o que cada uma pode e não pode fazer, compartilhar uma coleção publicamente enquanto mantém outra privada, e ajustar uma função depois do fato.

Vamos acompanhar Emma, que cataloga quadrinhos com o parceiro dela, Sam, e gosta de mostrar sua coleção ao amigo Leo. Sam ajuda com o cadastro de dados, então ele precisa poder editar. Leo só navega, então ele não deveria conseguir alterar nada.

Espere que isso leve cerca de quinze minutos, mais o tempo que seus convidados levarem para abrir o e-mail.

## Antes de começar

- Você precisa ser **proprietário** da conta. Só proprietários podem convidar pessoas e alterar funções.
- Leia @doc(accounts.usersAndRoles), se ainda não leu. A versão resumida: visualizadores leem, editores alteram o conteúdo do catálogo, proprietários também administram a conta.
- Saiba os e-mails dos seus convidados, e uma coisa sobre eles: um convite só funciona para um e-mail que ainda não tenha uma conta própria no KolleK, porque cada pessoa pertence a exatamente uma conta.

## Passo 1: Convide Sam como editor

::::steps
:::step title="Abra a área de membros"
Vá até as configurações de membros da sua conta, onde membros e convites pendentes são listados.

::screenshot{label="Tela de membros com o formulário de convite"}
:::

:::step title="Envie o convite"
Informe o **e-mail** de Sam, escolha a função **Editor** e envie. Sam agora pode criar e editar coleções, itens e exemplares, mas não pode convidar pessoas nem mexer nas configurações da conta.
:::
::::

O e-mail de convite contém um link válido por **sete dias**. Se ele expirar antes de Sam usá-lo, basta convidá-lo novamente.

## Passo 2: Convide Leo como visualizador

Repita os mesmos passos para Leo, mas deixe a função como **Visualizador**, que é o padrão. Leo vai poder navegar por tudo na conta, incluindo coleções, itens e seus históricos, mas todo controle de edição vai estar fora do alcance dele.

Escolher a função menor não é falta de gentileza. Isso também protege Leo: ele não pode excluir um item ou alterar um registro por acidente enquanto navega.

## Passo 3: O que Sam e Leo vivenciam

Cada um deles recebe um e-mail e abre o link. Como nenhum dos dois tem uma conta no KolleK ainda, a página pede que definam **nome**, **sobrenome** e uma **senha** (pelo menos oito caracteres, verificada contra vazamentos conhecidos). Depois eles chegam na conta da Emma, já verificados e conectados, com a função que ela escolheu.

Se o link disser que já existe uma conta para aquele e-mail, essa pessoa não pode entrar por esse convite. Essa situação e outros problemas com convites estão cobertos em @doc(troubleshooting.signIn).

## Passo 4: Defina a visibilidade de cada coleção

Funções controlam as pessoas dentro da conta. A @doc(sharing.overview, "visibilidade") registra para quem cada coleção é destinada, desde só você até qualquer pessoa com um link.

Emma tem duas coleções: "Meus Quadrinhos", que ela quer mostrar ao mundo um dia, e "Pesquisa de Desejos", que não é da conta de mais ninguém além dela.

::::steps
:::step title="Marque uma coleção como pública"
Em "Meus Quadrinhos", ela define a visibilidade como **Pública**, marcando-a como aquela que pretende compartilhar além da conta.
:::

:::step title="Marque a outra como privada"
"Pesquisa de Desejos" é definida como **Privada**, destinada só a ela. **Compartilhada**, a opção intermediária, marca uma coleção como destinada a todo membro da conta.
:::
::::

:::note
A visibilidade ainda não é aplicada de fato. Hoje, Sam e Leo ainda podem navegar por toda coleção na conta, inclusive as privadas, e não existe link público para compartilhar, então nada é visível fora da conta de jeito nenhum. Definir a visibilidade agora significa que cada coleção vai se comportar corretamente assim que o compartilhamento chegar. Veja @doc(troubleshooting.featureStatus).
:::

:::warning
Quando os links públicos chegarem, uma coleção pública vai poder ser vista por qualquer pessoa que tiver o link, sem precisar entrar na conta. Só marque uma coleção como pública se estiver confortável com todo item dela sendo visto.
:::

O passo a passo completo, incluindo como reverter, está em @doc(collections.share).

## Passo 5: Ajuste uma função depois

Algumas semanas depois, Leo começa a notar erros e quer corrigi-los ele mesmo. Emma abre a tela de membros, encontra Leo e muda a função dele de **Visualizador** para **Editor**. A mudança se aplica imediatamente. Funções são um botão giratório, não uma sentença perpétua, e rebaixar alguém funciona da mesma forma.

Uma salvaguarda para conhecer: uma conta precisa sempre manter pelo menos um **proprietário**. O KolleK vai se recusar a rebaixar ou remover o último proprietário, para que uma conta compartilhada nunca fique sem dono e sem gestão possível.

:::warning
Remover um membro exclui a conta de usuário dele e não pode ser desfeito pela tela de membros. Se alguém só precisa de menos acesso, mude a função em vez de removê-lo.
:::

## O resultado

A conta de Emma agora tem três pessoas com três níveis de confiança: Emma possui e administra, Sam cataloga ao lado dela, e Leo navega e, ultimamente, organiza. Uma coleção está marcada para o mundo, outra só para ela, pronta para o dia em que o compartilhamento for aplicado de fato. Nada nessa configuração é definitivo; funções e visibilidade podem mudar conforme as pessoas mudam.

## Erros comuns a evitar

- **Convidar todo mundo como editor por padrão.** Dê a função que a pessoa precisa hoje. Fazer um upgrade depois é um clique.
- **Presumir que privado já esconde uma coleção.** A visibilidade é registrada, mas ainda não é aplicada, então todo membro pode navegar por toda coleção hoje, privada ou não. Por enquanto, mantenha catálogos verdadeiramente pessoais em uma conta só sua.
- **Remover um membro para reduzir o acesso dele.** A remoção é destrutiva. Mudanças de função não são.

## Para onde ir depois

- A referência completa de quem pode fazer o quê está em @doc(collaboration.rolesInPractice, "Entendendo as três funções na prática").
- Administre a própria conta, nome, moeda e mais, em @doc(accounts.settings).
- Vai rodar a instância para o seu clube você mesmo? Veja @doc(tutorials.selfHostWithDocker, "Auto-hospede o KolleK com Docker").
