---
id: activity.logAndSentEmails
title: Seu registro de atividades e emails enviados
slug: registro-de-atividades-e-emails-enviados
section: conta-e-perfil
---

# Seu registro de atividades e emails enviados

O KolleK mantém dois registros sobre você que você pode consultar a qualquer momento: tudo o que você fez, e todo email que o sistema te enviou. Ambos vivem na área do seu perfil, e ambos existem pelo mesmo motivo, transparência. Quando você se pergunta "eu realmente mudei isso" ou "aquele email de link mágico foi mesmo enviado", a resposta está aqui.

## Seu registro de atividades

A @doc(activity.feedAndAuditTrail, "trilha de atividades") que percorre toda a conta tem uma visualização pessoal: um histórico completo das suas próprias ações, desde criar um item até mudar uma configuração. Abra-o na área do seu perfil.

Use-o para retraçar seus passos. Se o local de um exemplar parece errado, seu registro vai mostrar se você o moveu, e quando.

## Seus emails enviados

O KolleK registra todo email que te envia: links mágicos, convites que você recebeu, mensagens de verificação, e @doc(security.alertEmails, "alertas de segurança"). A área do seu perfil os lista, mais recentes primeiro, dez por página.

Cada entrada mostra o que foi enviado e quando. Onde o serviço de email da instância informa de volta, você também vai ver se a mensagem foi entregue, ou se ela retornou (bounce).

Essa lista é a forma mais rápida de investigar um email que não chegou:

- **O email aparece aqui, mas nunca chegou na sua caixa de entrada.** Verifique sua pasta de spam, e verifique se a entrada mostra um retorno.
- **O email não aparece aqui de jeito nenhum.** A ação que deveria ter disparado ele não aconteceu, então peça de novo.
- **Emails aparecem aqui, mas nenhum é entregue.** Em uma instância auto hospedada isso geralmente significa que a entrega de email ainda não foi configurada. Aponte quem administra sua instância para @doc(selfHosting.setupEmailDelivery, "configurar a entrega de email").

:::note
Esta página mostra os emails enviados para você. Ela é pessoal, como o resto do seu perfil, e outros membros não podem ver a sua lista.
:::

## Para onde ir depois

- Entenda o histórico de toda a conta em @doc(activity.feedAndAuditTrail).
- Sentindo falta de um email esperado? Siga @doc(troubleshooting.emailDelivery).
