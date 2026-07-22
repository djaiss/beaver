---
id: users.deleteSelf
title: Exclua seu usuário
slug: exclua-seu-usuario
section: seguranca-e-manutencao-dos-dados
---

# Exclua seu usuário

Excluir seu usuário remove você, a pessoa, do KolleK. Isso não é a mesma coisa que excluir a conta: a conta é o espaço de trabalho compartilhado, e **@doc(accounts.delete, "excluí-la")** destrói tudo para todo mundo. Esta página cobre como remover apenas a si mesmo.

## Antes de decidir

Duas situações parecem "excluir meu usuário", mas não são:

- **Você quer que tudo suma.** Se você é o proprietário e quer que o catálogo inteiro e o espaço de trabalho sejam removidos, isso é @doc(accounts.delete).
- **Você quer sair de uma conta compartilhada.** Excluir seu usuário remove você e deixa a conta e seu catálogo com os outros membros.

Se você for o único proprietário da conta e ainda houver outros membros, promova outra pessoa a proprietário primeiro em **@doc(collaboration.manageMembersAndRoles, "gerenciamento de membros")**, para que a conta não fique sem nenhum proprietário.

## Exclua a si mesmo

::::steps
:::step title="Abra as configurações do seu perfil"
Vá para as configurações do seu perfil e encontre a zona de risco no final da página.
:::

:::step title="Diga por que está saindo"
Um motivo é obrigatório (algumas palavras já bastam, pelo menos três caracteres). Ele vai para quem administra a instância e ajuda a melhorar o KolleK.

::screenshot{label="Formulário de exclusão de usuário com o campo de motivo"}
:::

:::step title="Confirme"
Confirme a exclusão na caixa de diálogo. Você é desconectado imediatamente e seu login para de funcionar.
:::
::::

:::warning
Excluir seu usuário é permanente. Seu login desaparece e não pode ser restaurado pelo aplicativo. Seu endereço de e-mail fica livre novamente, então você poderia se cadastrar em uma conta totalmente nova depois, mas ela começaria vazia.
:::

## O que acontece com seus vestígios

O histórico de atividades da conta mantém sua integridade: entradas que você criou registram seu nome como ele era na época, então o registro de auditoria do trabalho compartilhado não fica com lacunas quando você sai.

## Para onde ir agora

- Prefere uma limpeza automática em vez disso? Veja @doc(users.inactiveDeletion).
- Remover outra pessoa de uma conta compartilhada é feito em @doc(collaboration.manageMembersAndRoles).
