---
id: dataSafety.howProtected
title: Comment vos données sont protégées
slug: how-your-data-is-protected
section: core-concepts
---

# Comment vos données sont protégées

Un catalogue enregistre ce que vous possédez, ce que cela vaut, et où c'est conservé. C'est sensible par nature, et KolleK le traite comme tel. Cette page explique les protections en des termes accessibles, et se montre honnête sur leurs limites.

## Chiffré au repos

Les champs sensibles (noms, détails d'objets, valeurs, et bien plus) sont chiffrés dans la base de données à l'aide de la clé de chiffrement de l'instance. Quelqu'un qui obtiendrait une copie du fichier de base de données sans la clé trouverait les colonnes sensibles illisibles.

Cela se produit automatiquement. Il n'y a rien à activer ni à configurer en tant qu'utilisateur.

## Chaque changement est enregistré

KolleK conserve une piste d'audit des actions des utilisateurs. Quand Sam modifie un objet, l'enregistrement montre qui l'a fait, ce qui a changé, et quand, et cela alimente le flux d'activité du compte ainsi que le journal propre à chaque objet. Le nom de l'auteur est capturé au moment de l'action, afin que l'historique reste lisible même si l'utilisateur de cette personne est supprimé plus tard. Voir @doc(activity.feedAndAuditTrail).

## La limite honnête

:::note
Le chiffrement au repos protège le contenu stocké de la base de données. Ce n'est pas un chiffrement de bout en bout. L'application peut lire vos données afin de vous les afficher, et quiconque exploite l'instance détient la clé de chiffrement.
:::

En pratique, cela signifie que votre confiance suit l'opérateur. Si vous @doc(selfHosting.index, "vous auto-hébergez"), cet opérateur, c'est vous, et vous détenez la clé sur votre propre matériel. Si quelqu'un héberge KolleK pour vous, il détient techniquement la clé, exactement comme pour toute application web hébergée.

Deux conséquences à connaître.

- **La clé est précieuse.** Si elle est perdue, les données chiffrées ne peuvent être récupérées par personne. Les opérateurs devraient lire @doc(selfHosting.applicationKeyAndEncryption).
- **Les sauvegardes comptent.** Le chiffrement protège contre l'indiscrétion, pas contre la perte. Les personnes qui s'auto-hébergent devraient suivre @doc(selfHosting.backupAndRestore).

## Ce que vous contrôlez

Vous choisissez ce qui sort du compte. Aujourd'hui, rien ne sort : aucune collection n'est atteignable depuis l'extérieur de votre compte. Chaque collection porte un @doc(sharing.overview, "paramètre de visibilité") enregistrant à qui elle est destinée, et quand le partage arrivera, une collection que vous avez marquée publique deviendra la seule surface qu'un inconnu pourra jamais voir.

## Et ensuite

- Voyez qui a changé quoi dans @doc(activity.feedAndAuditTrail).
- Renforcez votre propre connexion avec @doc(security.index).
- Vous gérez votre propre instance ? Lisez @doc(selfHosting.applicationKeyAndEncryption).
