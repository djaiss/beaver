---
id: selfHosting.addLanguage
title: Adicione um idioma
slug: adicione-um-idioma
section: auto-hospedagem
---

# Adicione um idioma

O KolleK é distribuído em sete idiomas: inglês, francês, espanhol, alemão, português brasileiro, chinês simplificado e japonês. Cada usuário escolhe seu próprio idioma no perfil, e pode até trocá-lo pela página de acesso. Esta página explica como as traduções funcionam por baixo dos panos, e como um operador ou colaborador adiciona uma nova localidade ou completa uma já existente.

Se você só quer mudar o idioma que vê, não precisa de nada disso. Veja @doc(profile.changeLanguage).

## Como as traduções são armazenadas

Cada localidade é um arquivo JSON em `lang/`, nomeado a partir do código da localidade, por exemplo `lang/fr_FR.json`. Cada arquivo mapeia a string original em inglês para sua tradução. A lista de localidades que o aplicativo oferece é definida na configuração da aplicação como as localidades suportadas.

## Preparar ou atualizar uma localidade

O comando `kollek:localize` varre toda a aplicação em busca de strings traduzíveis e as sincroniza com o arquivo de uma localidade:

```
php artisan kollek:localize fr_FR
```

Strings novas desde a última execução são adicionadas, e strings que não existem mais são removidas. No arquivo em inglês, cada string é sua própria tradução, então o inglês está sempre completo por definição. Em qualquer outra localidade, strings novas chegam vazias, prontas para um tradutor preencher.

Adicionar um idioma totalmente novo segue o mesmo fluxo: registre a localidade na configuração de localidades suportadas, execute o comando com o novo código de localidade para gerar seu arquivo, e depois traduza as entradas vazias.

:::note
Uma tradução vazia recai para o inglês em vez de quebrar a interface, então uma localidade parcialmente traduzida continua utilizável enquanto o trabalho segue.
:::

## O que ainda não é traduzido

A aplicação logada é totalmente traduzível. O site institucional público e a referência de API gerada ainda não são traduzidos e sempre são exibidos em inglês, seja qual for a localidade de quem visita. Veja @doc(troubleshooting.featureStatus).

## Para onde ir depois

- Execute o comando na sua instância com @doc(selfHosting.cliCommands).
- Veja o lado do leitor sobre isso em @doc(profile.changeLanguage).
