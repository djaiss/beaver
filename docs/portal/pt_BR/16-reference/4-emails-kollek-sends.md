---
id: reference.emailsSent
title: E-mails que o KolleK envia
slug: emails-que-o-kollek-envia
section: referencia
---

# E-mails que o KolleK envia

Todo e-mail que o sistema pode enviar, o que o dispara e quem o recebe. Use esta página para reconhecer uma mensagem legítima, ou para verificar a entrega quando você administra uma instância.

O KolleK mantém um registro de todo e-mail que envia a você, incluindo status de entrega e de rejeição, na sua @doc(activity.logAndSentEmails, "página de e-mails enviados"). Operadores que ainda não configuraram um serviço de envio de e-mail devem ler @doc(selfHosting.setupEmailDelivery), porque uma instância recém-instalada apenas registra o e-mail e não envia nada.

## Entrando e permanecendo conectado

| E-mail | Disparado quando | Enviado para |
| --- | --- | --- |
| Convite de conta | Um proprietário convida alguém para a conta. O link do convite expira após sete dias. | O endereço convidado |
| Link mágico | Alguém solicita um link de login sem senha. O link é válido por cinco minutos. | O e-mail da conta |
| Verificação de e-mail | Você se cadastra, ou altera seu endereço de e-mail. | O novo endereço |
| Redefinição de senha | Você usa o link "esqueci minha senha". O link de redefinição é válido por 60 minutos. | O e-mail da conta |

## Alertas de segurança

Estes chegam sem você solicitar quando algo relevante acontece na sua conta. Veja @doc(security.alertEmails) para saber o que fazer quando um deles te surpreender.

| E-mail | Disparado quando | Enviado para |
| --- | --- | --- |
| Alerta de falha de login | Uma tentativa de login com senha falha em uma conta existente. | O e-mail da conta |
| Alerta de novo login | Um login bem-sucedido acontece, informando o dispositivo usado. | O e-mail da conta |
| Alerta de mudança de endereço IP | Um login acontece a partir de um endereço IP diferente do da última vez. | O e-mail da conta |
| Chave de API criada | Você cria uma chave de API manualmente. Tokens criados ao fazer login pela API não disparam este aviso. | O e-mail da conta |
| Chave de API excluída | Você exclui uma chave de API. | O e-mail da conta |

## Avisos para o operador

Estes vão para o endereço do operador configurado na instância, não para os colecionadores. Existem para que quem administra o servidor saiba quando as pessoas saem.

| E-mail | Disparado quando | Enviado para |
| --- | --- | --- |
| Usuário excluído | Uma pessoa exclui o próprio usuário, incluindo o motivo que ela informou. | O endereço do operador |
| Usuário excluído automaticamente | O sistema exclui um usuário que optou pela exclusão por inatividade e está inativo há seis meses. | O endereço do operador |

## Para onde ir agora

- Reconheça e reaja aos alertas: @doc(security.alertEmails).
- Faça o e-mail realmente ser enviado na sua instância: @doc(selfHosting.setupEmailDelivery).
- Verifique o que foi enviado para você: @doc(activity.logAndSentEmails, "Seu registro de atividades pessoal e e-mails enviados").
