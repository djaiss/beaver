---
id: selfHosting.setupEmailDelivery
title: Configure a entrega de e-mail
slug: configure-a-entrega-de-email
section: auto-hospedagem
---

# Configure a entrega de e-mail

O e-mail é como o KolleK alcança as pessoas fora de uma sessão no navegador: @doc(collaboration.invitePeople, "convites"), @doc(auth.magicLinks, "links mágicos"), redefinições de senha, verificação de e-mail e @doc(security.alertEmails, "alertas de segurança") chegam todos por e-mail. Até você configurar a entrega, nenhum deles vai a lugar nenhum.

## O padrão não envia nada

Uma instância recém-instalada vem com `MAIL_MAILER=log`. Cada e-mail é gravado no arquivo de log da aplicação em vez de ser enviado. Isso é proposital: significa que uma instância configurada pela metade nunca envia e-mail silenciosamente de um endereço errado, e você pode ler exatamente o que seria enviado enquanto testa.

:::note
Se alguém disser "nunca recebi o convite" em uma instância nova, esse padrão é quase sempre o motivo. O e-mail existe, no arquivo de log. Veja @doc(troubleshooting.emailDelivery).
:::

Você tem duas formas suportadas de enviar e-mail de verdade: qualquer servidor SMTP, ou o serviço Resend.

## Opção 1: SMTP

::::steps
:::step title="Defina o mailer e os detalhes do servidor"
No `.env`, defina:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

Qualquer provedor de e-mail transacional ou servidor de e-mail próprio com credenciais SMTP funciona.
:::

:::step title="Defina a identidade do remetente"
Defina o endereço e o nome que seus usuários vão ver:

```bash
MAIL_FROM_ADDRESS=kollek@yourdomain.com
MAIL_FROM_NAME="KolleK"
```

Use um domínio que você controla e que está configurado para envio (SPF e DKIM no seu provedor), ou seu e-mail vai cair no spam.
:::

:::step title="Aplique e teste"
Recrie os contêineres e depois dispare um e-mail real, por exemplo solicitando um link mágico na página de acesso:

```bash
docker compose up -d
```
:::
::::

## Opção 2: Resend

Se você usa o [Resend](https://resend.com), defina:

```bash
USE_RESEND=true
RESEND_API_KEY=re_your_api_key
```

Os e-mails passam então a ser enviados pela API do Resend em vez de SMTP, e cada envio registra o id da mensagem no Resend junto com ele.

## Verificando se a entrega funciona

O KolleK registra todo e-mail que envia, por usuário, com seu assunto, corpo e status de entrega. Depois do seu teste, verifique dois lugares:

- Sua caixa de entrada, pelo motivo óbvio.
- A página de **e-mails enviados** do destinatário, no perfil dele, que lista o que a instância enviou a ele. Veja @doc(activity.logAndSentEmails, "Seu histórico de atividade pessoal e e-mails enviados").

Sinais comuns de falha:

- **Nada chega e nada dá erro.** O mailer ainda é `log`. Verifique se o `.env` foi aplicado recriando os contêineres.
- **Os e-mails são enviados mas caem no spam.** O domínio do remetente não está autenticado. Configure SPF e DKIM no seu provedor.
- **Erros de envio no log.** As credenciais ou os detalhes do host estão errados. Os logs do worker da fila contêm a mensagem de erro do provedor.

Os e-mails são enviados pela fila em segundo plano, então o contêiner **queue** precisa estar rodando para qualquer coisa sair da instância.

## Para onde ir depois

- Reconheça os e-mails que sua instância envia em @doc(reference.emailsSent).
- Diagnostique problemas de entrega em @doc(troubleshooting.emailDelivery).
