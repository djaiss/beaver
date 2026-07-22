---
id: auth.magicLinks
title: Links mágicos explicados
slug: links-magicos
section: seguranca
---

# Links mágicos explicados

Um link mágico é uma forma de entrar sem senha. Em vez de digitar sua senha, você pede para o KolleK te enviar um link por email. Abra o link, e você está logado. Esta página explica como funciona, quando é conveniente, e a única consequência que você deve entender antes de depender dele.

## Peça um link mágico

Na página de login, escolha a opção de link mágico, digite seu **email** e envie. O KolleK manda um link de uso único para esse endereço. Abra-o, e você chega direto no seu painel.

Para sua privacidade, a página mostra a mesma confirmação existindo ou não uma conta para o endereço que você digitou, então ela nunca revela quem está cadastrado.

## As regras que ele segue

- **O link é válido por cinco minutos.** Se expirar antes de você abri-lo, peça outro. Nada se perde.
- **Ele vai apenas para o email da sua conta.** Você precisa ter acesso a essa caixa de entrada. Isso também é o que torna o link seguro: só quem consegue ler seu email pode usá-lo.
- **Ele funciona uma vez.** Um link que já fez seu login não pode ser reutilizado.

## A consequência com a autenticação de dois fatores

Entrar com um link mágico não pede um código de @doc(security.twoFactorAuth, "dois fatores").

Isso é proposital, não um descuido. Um link mágico já comprova duas coisas de uma vez: que a pessoa entrando conhece seu endereço de email, e que ela controla a caixa de entrada por trás dele. A caixa de entrada está atuando como o segundo fator.

:::warning
Se você usa autenticação de dois fatores, lembre que qualquer pessoa que controle sua caixa de entrada consegue entrar no KolleK com um link mágico, sem nunca ver seu autenticador. Sua conta de email é o portão de verdade, então proteja-a com uma senha forte e sua própria configuração de dois fatores.
:::

## Quando usar

Links mágicos são úteis quando:

- Você está em um dispositivo onde não quer digitar sua senha.
- Você esqueceu sua senha e só precisa entrar. Uma vez dentro, você pode @doc(auth.resetPassword, "definir uma nova senha") no seu perfil.
- Você prefere não usar uma senha no dia a dia e sua conta de email está bem protegida.

Prefira sua senha e o código do autenticador quando estiver em uma máquina compartilhada ou não confiável, onde você preferiria nem abrir sua caixa de entrada.

## Para onde ir depois

- Todos os caminhos de login em um só lugar: @doc(auth.signIn).
- Fortaleça a porta de entrada: @doc(security.twoFactorAuth).
- O link nunca chegou? Veja @doc(troubleshooting.emailDelivery).
