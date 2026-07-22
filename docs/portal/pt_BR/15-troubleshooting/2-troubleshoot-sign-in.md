---
id: troubleshooting.signIn
title: Solução de problemas de acesso
slug: solucao-de-problemas-de-acesso
section: solucao-de-problemas
---

# Solução de problemas de acesso

Ficou sem acesso, ou algo na tela de entrada não está fazendo o que você esperava? Encontre seu sintoma abaixo. Cada item traz a solução primeiro, depois um link para a explicação completa.

## Esqueci minha senha

Use o link **esqueci minha senha** na tela de entrada. Digite seu e-mail, abra o e-mail de redefinição e escolha uma nova senha. O link de redefinição expira depois de 60 minutos, então use-o logo, e peça outro se ele expirar.

Alternativa mais rápida: solicite um @doc(auth.magicLinks, "link mágico") em vez disso. Ele te dá acesso sem senha, e você pode definir uma nova senha depois, a partir do seu perfil.

Detalhes completos em @doc(auth.resetPassword).

## Minha nova senha continua sendo recusada

O KolleK exige pelo menos oito caracteres e recusa qualquer senha que já tenha aparecido em um vazamento público de dados. A recusa é sobre a senha em si, não sobre sua conta. Escolha algo mais longo e único, que você ainda não tenha usado em outro lugar. Veja @doc(auth.resetPassword).

## Perdi meu dispositivo de dois fatores

No desafio de dois fatores, digite um dos seus **códigos de recuperação** no lugar do código de seis dígitos. Cada código de recuperação funciona uma única vez. Depois de entrar, desative e reative a autenticação de dois fatores com seu novo dispositivo, para obter um novo pareamento e um novo conjunto de códigos.

Detalhes completos em @doc(security.recoveryCodes).

:::warning
Se você perdeu seu autenticador e não tem códigos de recuperação, não há forma autônoma de concluir a etapa de dois fatores. Em uma instância auto-hospedada, converse com quem opera seu servidor.
:::

## Meu link mágico não funciona

Links mágicos são válidos por **cinco minutos** e funcionam **uma vez**. Se o seu expirou ou já foi usado, solicite um novo na tela de entrada. Certifique-se de abrir o link no dispositivo onde você quer entrar.

Detalhes completos em @doc(auth.magicLinks).

## Tentei várias vezes e agora estou bloqueado

Tentativas repetidas e rápidas são limitadas para desacelerar tentativas de adivinhação de senha. Espere um minuto e tente de novo, com cuidado. Se você não tem certeza da senha, mude para o @doc(auth.resetPassword, "fluxo de redefinição") ou um @doc(auth.magicLinks, "link mágico") em vez de continuar tentando adivinhar.

## Recebi um e-mail de "falha ao entrar" que não reconheço

Alguém digitou seu e-mail com a senha errada. Veja @doc(security.alertEmails) para entender o que isso significa e quando agir.

## Meu link de convite não funciona

Duas causas comuns:

- **O convite expirou.** Convites duram sete dias. Peça ao proprietário da conta para enviar um novo.
- **Seu e-mail já tem um usuário no KolleK.** Uma pessoa pertence a exatamente uma conta, então um convite não pode ser aceito por um e-mail que já tem uma conta própria.

Detalhes completos em @doc(collaboration.invitePeople).

## O e-mail que estou esperando nunca chega

O e-mail de redefinição, o link mágico ou o convite podem não estar chegando até você. Isso costuma ser um problema de entrega, não um problema de acesso. Veja @doc(troubleshooting.emailDelivery).

## Próximos passos

- O básico de todo fluxo de entrada: @doc(auth.signIn).
- Reforce sua segurança depois de voltar a acessar a conta: @doc(security.index).
