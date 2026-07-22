---
id: apiKeys.manage
title: Gérer les clés API
slug: gerer-les-cles-api
section: securite
---

# Gérer les clés API

Une clé API est un jeton personnel qui permet à un script ou une application d'agir en votre nom via l'API KolleK. Cette page couvre son cycle de vie : créer une clé, la suivre, et la révoquer. Ce que vous pouvez réellement faire avec une clé se trouve dans la @doc(api.authenticate, "section développeurs").

Si vous ne comptez jamais utiliser l'API, vous pouvez ignorer cette page entièrement. Aucune clé n'existe tant que vous n'en créez pas une.

## Créer une clé

::::steps
:::step title="Ouvrez vos paramètres de clés API"
Rendez vous sur votre profil et ouvrez la zone des clés API. Vous verrez les clés que vous avez déjà, chacune avec la date de sa dernière utilisation.
:::

:::step title="Nommez la nouvelle clé"
Choisissez de créer une clé et donnez lui un **libellé** qui indique à quoi elle sert, comme « Script d'import » ou « Tableau de bord domestique ». Les libellés sont là pour votre futur vous, qui devra décider quelle clé peut être révoquée sans risque.
:::

:::step title="Copiez le jeton immédiatement"
KolleK affiche le jeton une seule fois, juste après la création. Copiez le maintenant et conservez le dans un endroit sûr, comme un gestionnaire de mots de passe.

::screenshot{label="Nouvelle clé API avec le jeton révélé une seule fois"}
:::
::::

:::warning
Le jeton n'est affiché qu'une seule fois. Si vous le perdez, vous ne pourrez plus le consulter. Révoquez la clé et créez en une nouvelle.
:::

KolleK vous envoie un avis par email chaque fois qu'une clé est créée sur votre utilisateur, afin qu'une clé inattendue ne passe jamais inaperçue.

## Suivre vos clés

La zone des clés API liste chaque clé avec son libellé et sa dernière date d'utilisation. Cette dernière date d'utilisation est votre alliée : une clé qui n'a pas été utilisée depuis des mois est une clé que vous pouvez probablement révoquer, et une clé utilisée il y a cinq minutes alors que votre script n'a pas tourné est une clé à examiner.

Une habitude facilite la gestion : une clé par usage. Lorsque chaque intégration a sa propre clé, vous pouvez en révoquer une sans casser les autres.

## Révoquer une clé

Supprimez la clé depuis la même liste. Tout ce qui utilise encore son jeton cesse de fonctionner immédiatement, et KolleK vous envoie un avis de suppression par email.

Révoquez une clé quand.

- Vous n'utilisez plus le script ou l'application auquel elle appartenait.
- Le jeton a peut être fuité, par exemple en étant commité dans un dépôt ou partagé dans une conversation.
- Vous avez reçu une @doc(security.alertEmails, "alerte de création ou de suppression de clé") que vous ne reconnaissez pas. Dans ce cas, changez aussi votre mot de passe.

:::note
Se connecter via l'API crée également un jeton en coulisses. Ces jetons de connexion ne déclenchent pas l'email de création de clé, afin que les alertes que vous recevez restent pertinentes.
:::

## Et ensuite

- Mettez une clé à profit avec votre première requête : @doc(api.authenticate).
- Comprenez les emails liés aux clés : @doc(security.alertEmails).
