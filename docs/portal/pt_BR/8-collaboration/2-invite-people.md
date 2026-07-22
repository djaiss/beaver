---
id: collaboration.invitePeople
title: Convide pessoas para sua conta
slug: convide-pessoas-para-sua-conta
section: colaboracao
---

# Convide pessoas para sua conta

Catalogar é mais divertido, e mais preciso, quando as pessoas que compartilham a coleção também compartilham o catálogo. Esta página mostra como convidar alguém para sua conta, o que essa pessoa vai vivenciar, e os limites que você precisa conhecer antes de enviar o convite.

Só um **@doc(accounts.usersAndRoles, "proprietário")** pode convidar pessoas. Se você não vê essa opção, peça a um proprietário da sua conta.

## Decida a função primeiro

Todo convite carrega uma **@doc(collaboration.rolesInPractice, "função")**, escolhida no momento em que você convida:

- **Visualizador** pode navegar por tudo, mas não muda nada. Esse é o padrão.
- **Editor** pode criar e alterar o conteúdo do catálogo.
- **Proprietário** pode fazer tudo, incluindo gerenciar membros e configurações da conta.

Comece as pessoas na função mais baixa que fizer sentido. Você sempre pode **@doc(collaboration.manageMembersAndRoles, "elevar depois")**, o que é mais fácil do que retirar um acesso que alguém não deveria ter tido.

A Emma, por exemplo, convida seu parceiro Sam como **editor**, para que ele também possa adicionar quadrinhos, e seu amigo Leo como **visualizador**, para que ele possa navegar pela coleção sem poder alterá-la.

## Envie um convite

::::steps
:::step title="Abra os membros da sua conta"
Vá para as configurações da sua conta e abra a área de membros. Você vai ver os membros atuais e qualquer convite pendente.

::screenshot{label="Tela de membros com o formulário de convite"}
:::

:::step title="Digite o endereço de e-mail e escolha uma função"
Digite o **endereço de e-mail** da pessoa e escolha sua **função**. Se você não mexer na função, ela vai entrar como visualizadora.
:::

:::step title="Envie"
Envie o formulário. O KolleK manda um e-mail para a pessoa com um link para entrar na sua conta, e o convite aparece na sua lista de pendentes.
:::
::::

Se você convidar o mesmo e-mail novamente enquanto um convite anterior ainda está pendente e não expirou, o KolleK reutiliza o convite existente em vez de acumular duplicatas.

## O que a pessoa convidada vivencia

A pessoa recebe um e-mail com um link. Ao abri-lo, ela vê quem a convidou e para qual conta. Para entrar, ela preenche seu **nome**, **sobrenome** e uma **senha**. As mesmas proteções de senha do cadastro se aplicam: pelo menos oito caracteres, e nada que já tenha aparecido em um vazamento conhecido.

Depois de enviar, ela se torna membro da sua conta na função que você escolheu, seu e-mail já está verificado, e ela já está conectada. Não há mais nada para você fazer.

## Os limites que você precisa conhecer

:::note
Convites expiram após sete dias. Se alguém perder o prazo, basta convidar de novo.
:::

Um limite merece atenção especial, porque é o motivo mais comum de um convite falhar:

- **Uma pessoa pertence a exatamente uma conta.** Se o e-mail que você convida já tem sua própria conta no KolleK, essa pessoa não consegue aceitar seu convite. Ela precisaria usar um endereço de e-mail diferente, ou **@doc(users.deleteSelf, "excluir seu usuário existente")** primeiro.
- **Só proprietários podem convidar.** Editores e visualizadores não podem trazer pessoas novas.

Se um e-mail de convite nunca chegar, a entrega de e-mail da instância pode ainda não estar configurada. Veja **@doc(troubleshooting.emailDelivery, "solução de problemas de entrega de e-mail")**.

## Para onde ir agora

- Ajuste o acesso ou remova alguém em @doc(collaboration.manageMembersAndRoles).
- Confira exatamente o que cada função permite em @doc(collaboration.rolesInPractice).
- Percorra uma configuração completa no tutorial @doc(tutorials.inviteHousehold, "Convide sua família ou clube").
