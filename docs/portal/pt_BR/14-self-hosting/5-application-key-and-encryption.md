---
id: selfHosting.applicationKeyAndEncryption
title: A chave da aplicação e a criptografia
slug: chave-da-aplicacao-e-criptografia
section: auto-hospedagem
---

# A chave da aplicação e a criptografia

Esta página explica a regra operacional mais importante de rodar o KolleK. Tudo o mais sobre a instância é recuperável com paciência. Esta é a única configuração que pode destruir dados de forma irreversível.

## O que a chave faz

O KolleK criptografa campos sensíveis em repouso com a chave da aplicação da instância, o valor `APP_KEY` no seu `.env`. Nomes, detalhes de itens, valores de campos personalizados, nomes de arquivos, registros de e-mail, segredos de webhook: cerca de trinta modelos carregam colunas criptografadas. O que fica gravado no banco de dados para esses campos é texto cifrado, ilegível sem a chave. A mesma chave também protege as sessões dos usuários.

É isso que @doc(dataSafety.howProtected) descreve do ponto de vista do usuário. Operacionalmente, isso significa que a chave não é um detalhe de configuração. Ela é metade dos seus dados.

## A regra

:::warning
Defina a chave da aplicação uma vez, antes do primeiro boot, e nunca a altere em uma instância em produção. Se a chave for perdida ou alterada, toda coluna criptografada e toda sessão se tornam permanentemente ilegíveis. Não há recuperação, não há caminho de suporte e não há ferramenta capaz de trazer os dados de volta.
:::

Três consequências práticas:

- **Faça backup da chave junto com os dados.** Um backup de banco de dados sem sua chave correspondente restaura apenas texto cifrado. Guarde a chave em um gerenciador de senhas ou cofre de segredos, separado do servidor.
- **Mantenha-a idêntica em todos os lugares.** Os três contêineres da aplicação (web, queue, scheduler) precisam rodar com a mesma chave. O arquivo Compose fornecido compartilha um único `.env`, o que já resolve isso. Preserve essa característica em qualquer implantação personalizada.
- **Não a regenere "por segurança".** Rodar `key:generate` em uma instância em produção é o desastre autoinfligido clássico. A instância se recusa a iniciar sem uma chave justamente para que ninguém suba uma acidentalmente sem chave e gere uma nova no meio do caminho.

## Rotacionando a chave deliberadamente

Alguns operadores precisam rotacionar chaves em um cronograma por motivos de política. O KolleK suporta isso através de chaves anteriores: a `APP_KEY` atual criptografa tudo que é novo, enquanto as chaves listadas em `APP_PREVIOUS_KEYS` (separadas por vírgula) ainda conseguem descriptografar os dados existentes.

```bash
APP_KEY=base64:NEW_KEY_HERE
APP_PREVIOUS_KEYS=base64:OLD_KEY_HERE
```

Gere uma nova chave com `php artisan key:generate --show` (nunca o `key:generate` puro, que sobrescreve sua chave em produção), mova a chave antiga para `APP_PREVIOUS_KEYS`, defina a nova como `APP_KEY` e recrie os contêineres.

:::warning
Nunca remova uma chave de `APP_PREVIOUS_KEYS` enquanto ainda existir algum dado que ela criptografou. Os dados só são recriptografados com a nova chave quando são gravados novamente, então registros antigos podem depender da chave antiga por tempo indefinido.
:::

Se a rotação não for exigida de você, a política segura mais simples é: uma chave, definida uma vez, com bom backup.

## Para onde ir depois

- Garanta que a chave faça parte do seu @doc(selfHosting.backupAndRestore, "plano de backup e restauração").
- Leia a visão da criptografia voltada ao usuário em @doc(dataSafety.howProtected).
