---
id: auth.resetPassword
title: Redefina sua senha
slug: redefina-sua-senha
section: seguranca
---

# Redefina sua senha

Seja porque você esqueceu sua senha ou simplesmente quer uma nova, esta página cobre os dois caminhos: recuperar o acesso a partir da página de login, e trocar sua senha deliberadamente a partir do seu perfil.

## Se você esqueceu sua senha

1. Na página de login, escolha o link **esqueci minha senha**.
2. Digite seu endereço de email e envie.
3. Abra o email que o KolleK te enviou e siga o link de redefinição.
4. Escolha uma nova senha e confirme-a. Agora você pode entrar com ela.

Dois comportamentos aqui valem a pena conhecer para que não te confundam:

- **A mensagem de confirmação é sempre a mesma**, existindo ou não uma conta para o endereço que você digitou. Isso protege sua privacidade ao nunca revelar quem está cadastrado. Se você tem uma conta, o email vai chegar.
- **O link de redefinição expira depois de 60 minutos.** Se você abri-lo tarde demais, é só pedir outro.

:::note
Se preferir pular a redefinição por completo, um @doc(auth.magicLinks, "link mágico") pode fazer seu login sem senha. Uma vez dentro, você pode definir uma nova senha no seu perfil.
:::

## Se você só quer trocá-la

Você não precisa do fluxo de esqueci minha senha para trocar sua senha. Vá até seu perfil, abra a área de segurança, e troque sua senha por lá. Você vai digitar sua senha atual e escolher a nova.

## Por que uma senha pode ser recusada

O KolleK verifica toda senha nova contra duas regras, então uma recusa nunca é um mistério:

- **Pelo menos oito caracteres.** Senhas mais curtas são recusadas de imediato.
- **Nenhuma senha conhecida por ter vazado.** Sua senha candidata é verificada contra listas de senhas que já apareceram em vazamentos públicos de dados. Se ela já vazou em algum lugar, é recusada, mesmo que pareça forte. Isso é sobre a senha em si, não sobre sua conta, então escolha algo que você não tenha usado em outros sites.

Um gerenciador de senhas contorna as duas regras sem esforço, gerando algo longo e único.

## Para onde ir depois

- Adicione uma segunda etapa para que uma senha roubada não seja suficiente: @doc(security.twoFactorAuth).
- Ainda não consegue entrar? Siga @doc(troubleshooting.signIn).
- O email de redefinição nunca chegou? Veja @doc(troubleshooting.emailDelivery).
