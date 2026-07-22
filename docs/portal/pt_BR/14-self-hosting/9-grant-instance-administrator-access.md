---
id: instanceAdmin.grantAccess
title: Conceda acesso de administrador de instância
slug: conceda-acesso-de-administrador-de-instancia
section: auto-hospedagem
---

# Conceda acesso de administrador de instância

Um administrador de instância é a pessoa que cuida do próprio servidor, com um painel que enxerga todas as contas da instância. Esta página explica o que é essa flag, como concedê-la e as salvaguardas ao redor dela.

## O que a flag é, e o que não é

A flag de administrador de instância é global ao servidor e completamente separada das @doc(accounts.usersAndRoles, "funções da conta"). Ela concede exatamente uma coisa: acesso ao @doc(instanceAdmin.panel, "painel de administração da instância").

- Ela não dá nenhum poder extra dentro da própria conta do administrador. Um administrador de instância que é visualizador na sua própria conta ainda não consegue editar itens ali.
- É por usuário, não por conta. Conceda a flag à pessoa específica que opera o servidor, tipicamente você mesmo.

Alex, que opera a instância do clube, mantém a flag no próprio usuário e é um proprietário comum dentro da própria conta. Os dois fatos não têm relação.

## Conceder e revogar

A flag é gerenciada pela linha de comando, o que é proposital: o acesso inicial ao painel global do servidor deveria exigir acesso ao servidor.

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

Revogue-a da mesma forma:

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com --revoke
```

Um administrador já existente também pode alternar a flag de outros usuários de dentro do painel.

## Por que o painel finge não existir

Para quem não tem a flag, `/instance-admin` responde **404 Not Found**, não "acesso negado". O painel não anuncia sua existência para quem não pode usá-lo, então investigar uma instância não revela nada. Se você concedeu a flag a si mesmo e ainda assim vê um 404, verifique se está conectado exatamente com o usuário ao qual a concedeu.

## As salvaguardas contra bloqueio

Duas regras protegem a instância de perder seu administrador:

- Um administrador não pode revogar a própria flag pelo painel.
- Um administrador não pode excluir o próprio usuário pelo painel.

Assim, o painel nunca pode ser usado para bloquear a todos fora dele. E mesmo que todos os administradores desaparecessem, o caminho pela linha de comando acima sempre funciona, porque exige apenas acesso ao servidor.

## Para onde ir depois

- Veja o que o painel pode fazer em @doc(instanceAdmin.panel).
- Conheça os outros comandos de operador em @doc(selfHosting.cliCommands).
