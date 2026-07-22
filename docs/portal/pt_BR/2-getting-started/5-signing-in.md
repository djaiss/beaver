---
id: auth.signIn
title: Fazendo login
slug: fazendo-login
section: primeiros-passos
---

# Fazendo login

O KolleK oferece algumas formas de fazer login. Esta página cobre cada uma delas para que você escolha a que mais combina com você, e te indica o lugar certo caso fique bloqueado.

## Entrar com e-mail e senha

A forma usual. Vá para a página de login, digite o **e-mail** e a **senha** que você usou no cadastro, e envie. Você chega ao seu painel.

Se a **@doc(security.twoFactorAuth, "autenticação de dois fatores")** estiver ativada na sua conta, você será solicitado a informar um código logo após a senha. Veja abaixo.

## Entrar com um link mágico

Se preferir não digitar uma senha, o KolleK pode te enviar por e-mail um link que faz login por você.

Na página de login, escolha a opção de link mágico, digite seu **e-mail** e envie. O KolleK envia um link de uso único para esse endereço. Abra-o, e você estará conectado.

Duas coisas a saber:

- **O link é válido por cinco minutos.** Se ele expirar, basta solicitar outro.
- **O link vai para o e-mail da sua conta**, então você precisa ter acesso a essa caixa de entrada. Isso também é o que mantém tudo seguro: só quem consegue ler seu e-mail pode usá-lo.

## A etapa de dois fatores

Se você ativou a autenticação de dois fatores, entrar com sua senha exige um passo extra. Depois que sua senha é aceita, o KolleK pede o código atual do seu aplicativo autenticador. Digite-o para concluir o login.

Se você não conseguir acessar seu autenticador, pode digitar um dos seus **@doc(security.recoveryCodes, "códigos de recuperação")** no lugar. Cada código de recuperação funciona uma única vez.

:::warning
Entrar com um link mágico não pede um código de dois fatores, porque o acesso à sua caixa de entrada de e-mail já age como um segundo fator. Se você depende da autenticação de dois fatores, tenha isso em mente ao escolher como fazer login, e proteja sua conta de e-mail de acordo.
:::

Configurar a autenticação de dois fatores e salvar códigos de recuperação são assuntos cobertos na seção **Segurança** desta documentação.

## Esqueceu sua senha

Se você não conseguir lembrar sua senha, use o link "esqueci minha senha" na página de login. Digite seu e-mail, e o KolleK envia um link de redefinição.

Para sua privacidade, o KolleK sempre mostra a mesma mensagem de confirmação, exista ou não uma conta para aquele endereço, então a página não vai revelar quem está cadastrado. Se você tiver uma conta, o e-mail de redefinição vai chegar. Se você usar um link mágico para entrar, pode redefinir sua senha depois, a partir do seu perfil.

## Para onde ir agora

- Novo por aqui e ainda se configurando? Volte para @doc(gettingStarted.checklist).
- Quer uma proteção mais forte? Ative a autenticação de dois fatores na seção **Segurança**.
