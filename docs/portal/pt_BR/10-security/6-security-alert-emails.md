---
id: security.alertEmails
title: Emails de alerta de login e segurança
slug: emails-de-alerta-de-seguranca
section: seguranca
---

# Emails de alerta de login e segurança

De vez em quando, o KolleK pode te enviar um email sem que você tenha pedido nada. Esses alertas existem para garantir que, quando algo acontece em torno do seu usuário, você saiba disso pelo KolleK antes de saber de qualquer outra forma. Esta página lista cada alerta, o que ele significa, e o que fazer se algum te surpreender.

## Tentativa de login falhou

**Quando chega:** alguém digitou seu email com a senha errada na página de login.

**Se foi você**, errando sua própria senha, ignore.

**Se não foi você**, alguém está tentando acessar com seu endereço. Uma tentativa falha geralmente é só ruído, mas alertas repetidos significam que seu email está sendo alvo. Certifique-se de que sua senha é exclusiva do KolleK, e ative a @doc(security.twoFactorAuth, "autenticação de dois fatores") para que uma senha adivinhada não seja suficiente.

## Novo login

**Quando chega:** um login bem sucedido aconteceu, e o email nomeia o dispositivo de origem.

**Se foi você**, em um navegador, celular ou computador novo, ignore.

**Se não foi você**, alguém tem sua senha. @doc(auth.resetPassword, "Troque sua senha") imediatamente, e revise sua conta em busca de algo inesperado.

## Mudança de endereço IP

**Quando chega:** você fez login a partir de um endereço de rede diferente do da última vez.

Isso é normal quando você viaja, troca de rede, ou seu provedor alterna endereços. Só merece atenção se vier junto com um login que você não reconhece.

## Chave de API criada, chave de API excluída

**Quando chega:** uma @doc(apiKeys.manage, "chave de API") foi criada ou revogada no seu usuário.

**Se foi você**, gerenciando suas chaves, ignore.

**Se não foi você**, leve a sério. Uma chave inesperada significa que alguém teve acesso suficiente para criar uma. Revogue a chave, troque sua senha, e verifique suas chaves restantes e o horário do último uso de cada uma.

:::note
Tokens de login criados quando você entra pela API não disparam o email de chave criada. Só as chaves que você cria manualmente disparam, então o alerta continua significativo.
:::

## Emails que você pediu

Dois outros emails chegam só porque alguém os solicitou, então eles não são alertas por si só: o email de @doc(auth.magicLinks, "link mágico"), e o email de redefinição de senha. Se você receber um que não pediu, alguém digitou seu endereço naquele formulário. Nenhum dos dois pode ser usado sem acesso à sua caixa de entrada, mas emails não solicitados repetidos são outro sinal de que seu endereço está sendo testado.

## Se algo realmente parecer errado

1. @doc(auth.resetPassword, "Troque sua senha").
2. Ative a @doc(security.twoFactorAuth, "autenticação de dois fatores") se estiver desativada.
3. Revise suas @doc(apiKeys.manage, "chaves de API") e revogue qualquer uma que você não reconhece.
4. Confira @doc(activity.logAndSentEmails, "seu registro de atividades pessoal") em busca de ações que você não fez.

## Para onde ir depois

- Veja tudo o que o KolleK já te enviou, com status de entrega: @doc(activity.logAndSentEmails, "Seu registro de atividades pessoal e emails enviados").
- O catálogo completo de todo email que o KolleK pode enviar: @doc(reference.emailsSent).
