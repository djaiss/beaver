---
id: selfHosting.index
title: Visão geral da auto-hospedagem
slug: auto-hospedagem
section: auto-hospedagem
---

# Visão geral da auto-hospedagem

Rodar sua própria instância do KolleK é uma forma de primeira classe, totalmente suportada, de usar o produto, e é gratuita. Esta página explica no que você está se metendo antes de instalar qualquer coisa, e entrega a única regra que importa mais do que todas as outras.

Se você ainda não decidiu entre auto-hospedagem e uma instância hospedada, comece por @doc(kollek.hostingOptions).

## O que envolve rodar uma instância

O KolleK é distribuído como uma única imagem Docker que desempenha três papéis, escolhidos por uma variável de ambiente:

- O papel **web** serve a própria aplicação.
- O papel **queue** processa jobs em segundo plano (envio de e-mail, entregas de webhook, registro de logs).
- O papel **scheduler** executa os jobs de manutenção diários.

O arquivo Docker Compose fornecido inicia os três papéis, além de um banco de dados MySQL. Sessões, cache e a fila de jobs são todos baseados em banco de dados, então não há Redis ou nenhum outro serviço extra para operar. Fotos e documentos enviados ficam em um volume de armazenamento, em disco local por padrão, com suporte a armazenamento compatível com S3.

Os requisitos são modestos: uma máquina com Docker Engine 24 ou mais recente e o plugin Compose. Um pequeno servidor virtual roda uma instância pessoal sem dificuldades.

## A única regra que você precisa gravar agora

O KolleK criptografa dados sensíveis em repouso usando a chave da aplicação da sua instância.

:::warning
Defina a chave da aplicação uma vez, antes do primeiro boot, e nunca a altere em uma instância em produção. Se a chave mudar, todo campo criptografado e toda sessão se tornam permanentemente ilegíveis. Trate a chave como se fosse os próprios dados: faça backup dela e mantenha-a idêntica em todos os contêineres.
:::

Vale a pena ler sobre isso com calma antes de instalar. @doc(selfHosting.applicationKeyAndEncryption) explica o que a chave protege, como armazená-la e a única forma segura de rotacioná-la deliberadamente.

## Suas responsabilidades

Auto-hospedar significa que você é o operador. Na prática, isso é:

- **Instalação e atualizações.** Ambas são procedimentos Docker curtos e documentados.
- **Backups.** Não existe backup automático dentro do aplicativo. Você mesmo faz o backup do banco de dados e do volume de armazenamento, junto com a chave da aplicação.
- **Entrega de e-mail.** Uma instância recém-instalada registra o e-mail em log em vez de enviá-lo, então convites e links de acesso não chegam a lugar nenhum até você configurar um serviço de e-mail.
- **Manter os três papéis rodando.** Em particular, os jobs em segundo plano e a manutenção diária param silenciosamente se os contêineres de fila ou agendador estiverem parados.

Alex, que administra uma instância para o clube de colecionadores dele, gasta poucos minutos por mês nisso depois que a configuração inicial está pronta. Não é um fardo operacional pesado, mas é seu.

## Esta seção

Percorra as páginas mais ou menos nesta ordem:

1. @doc(selfHosting.installDocker). Do zero a uma instância rodando.
2. @doc(selfHosting.configure). As variáveis de ambiente que você realmente vai mexer.
3. @doc(selfHosting.setupEmailDelivery). Faça convites e links mágicos serem enviados de fato.
4. @doc(selfHosting.applicationKeyAndEncryption). A regra operacional mais importante.
5. @doc(selfHosting.upgrade). Migre para uma nova versão com segurança.
6. @doc(selfHosting.backupAndRestore). Proteja os dados.
7. @doc(selfHosting.scheduledJobs). O que o aplicativo faz sozinho todas as noites.
8. @doc(instanceAdmin.grantAccess). Inicialize o administrador de toda a instância.
9. @doc(instanceAdmin.panel). O que esse administrador pode ver e fazer.
10. @doc(selfHosting.cliCommands). Os comandos artisan que um operador precisa.
11. @doc(selfHosting.addLanguage). Como a interface é traduzida.

## Para onde ir depois

- Pronto para instalar? Vá para @doc(selfHosting.installDocker).
- Prefere um passo a passo guiado, do início ao fim? Siga o @doc(tutorials.selfHostWithDocker, "tutorial de auto-hospedagem").
