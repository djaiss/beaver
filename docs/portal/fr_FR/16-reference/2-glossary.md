---
id: reference.glossary
title: Glossaire
slug: glossary
section: reference
---

# Glossaire

Chaque terme du produit rassemblé en un seul endroit. Chaque entrée renvoie vers la page qui explique l'idée en détail. Les termes sont listés dans l'ordre où vous les rencontrez dans le produit, de l'espace de travail jusqu'aux enregistrements d'un exemplaire donné.

## L'espace de travail

**Compte.** Votre espace de travail privé, et la limite autour de tout ce que vous créez. Chaque collection, objet et paramètre vit dans exactement un compte. Voir @doc(accounts.usersAndRoles).

**Utilisateur.** Une personne qui se connecte. Un utilisateur appartient à exactement un compte et ne peut pas rejoindre un second compte avec la même adresse email. Voir @doc(accounts.usersAndRoles).

**Rôle.** Ce qu'un utilisateur est autorisé à faire dans son compte : un lecteur consulte, un éditeur catalogue, un propriétaire gère aussi le compte. Voir @doc(collaboration.rolesInPractice, "Comprendre les trois rôles en pratique").

## Le catalogue

**Collection.** Un groupe de premier niveau que vous nommez, tel que « Ma collection de comics » ou « Cave à vin ». Les collections contiennent des objets et ont leur propre devise et leur propre visibilité. Voir @doc(collections.overview).

**Type de collection.** Une sorte de chose que vous collectionnez (Comics, Disques vinyles, Vin) qui détermine quels champs personnalisés ses objets enregistrent. Les types sont partagés au sein du compte. Voir @doc(collectionTypes.overview).

**Champ personnalisé.** Un détail que vous définissez sur un type de collection, tel que « N° d'épisode » ou « Millésime ». Sa valeur est enregistrée sur chaque objet. Voir @doc(collectionTypes.overview).

**Groupe de champs.** Une section nommée, telle que « Informations d'édition », qui garde une longue liste de champs personnalisés lisible sur le formulaire de l'objet. Voir @doc(collectionTypes.setup).

**Objet.** Le type de chose que vous cataloguez, tel que « Amazing Spider-Man #1 ». Les détails descriptifs vivent sur l'objet, les choses physiques que vous possédez sont ses exemplaires. Voir @doc(items.itemsVsCopies).

**Exemplaire.** Une instance physique d'un objet que vous détenez réellement. Chaque exemplaire a son propre état, son propre emplacement, sa propre valeur et son propre historique. Voir @doc(items.itemsVsCopies).

## Regrouper et retrouver

**Catégorie.** Un outil de classement à l'intérieur d'une collection. Les catégories peuvent s'imbriquer, comme Marvel à l'intérieur de Comics. Voir @doc(organizing.categoriesSetsAndSeries).

**Ensemble.** Une liste finie que vous essayez de compléter au sein d'une collection, suivie par rapport à un nombre cible. Voir @doc(organizing.categoriesSetsAndSeries).

**Série.** Une franchise qui peut s'étendre sur plusieurs collections, telle que Harry Potter à travers les livres et les films. Une série ne suit pas de complétion. Voir @doc(organizing.categoriesSetsAndSeries).

**Étiquette.** Un libellé libre partagé sur toutes les collections du compte, tel que « Dédicacé ». Un objet peut porter plusieurs étiquettes. Voir @doc(tags.overview).

**Emplacement.** L'endroit où vit physiquement un exemplaire. Les emplacements s'imbriquent pour représenter des espaces réels, tels qu'une boîte sur une étagère dans une pièce. Voir @doc(locations.overview).

**État.** Une note décrivant l'état d'un exemplaire, telle que Neuf ou Endommagé. Voir @doc(conditions.overview).

## L'historique d'un exemplaire

**Transaction.** Un événement financier ou de propriété sur un exemplaire, tel qu'un achat ou une vente. Tout l'argent vit dans les transactions. Voir @doc(copies.recordPaymentsAndValue).

**Estimation.** Ce que valait un exemplaire à un instant donné. La valeur estimée actuelle d'un exemplaire est sa dernière estimation. Voir @doc(copies.recordPaymentsAndValue).

**Enregistrement d'assurance.** La couverture enregistrée pour un exemplaire : assureur, valeur assurée, détails de la police, et statut. Voir @doc(copies.insure).

**Prêt.** Un enregistrement de garde pour un exemplaire que vous avez prêté ou emprunté, avec ses dates, la partie concernée, et les détails de retour. Voir @doc(loans.lendAndBorrow).

**Enregistrement d'entretien.** Un travail de soin ou de réparation effectué sur un exemplaire, tel qu'un nettoyage ou une restauration. Voir @doc(copies.recordMaintenance).

**Événement de provenance.** Un chapitre de l'histoire de propriété et d'authenticité d'un exemplaire, tel qu'une acquisition, une exposition ou une expertise. Voir @doc(copies.traceProvenance).

**Historique d'emplacement.** L'enregistrement daté des lieux où un exemplaire a vécu au fil du temps. Déplacer un exemplaire clôt un enregistrement et en ouvre un nouveau. Voir @doc(copies.move).

**Document.** Un fichier ou un lien externe conservé avec un exemplaire ou l'un de ses enregistrements, tel qu'un reçu joint à une transaction. Voir @doc(copies.attachDocuments).

## Accès et sécurité

**Visibilité.** Un paramètre de collection qui enregistre à qui elle est destinée : privée (vous seul), partagée (tout le monde dans le compte), ou publique (toute personne disposant du lien, en lecture seule). Enregistrée dès aujourd'hui, appliquée une fois le partage disponible. Voir @doc(sharing.overview).

**Corbeille.** L'endroit où les collections, objets, exemplaires, catégories et listes supprimés attendent avant d'être purgés, et depuis lequel ils peuvent être restaurés. Voir @doc(dataSafety.restoreFromTrash).

**Administrateur d'instance.** Un indicateur valable pour tout le serveur, distinct des rôles de compte, qui débloque le panneau d'administration pour la personne qui exploite l'instance. Voir @doc(instanceAdmin.grantAccess).

**Clé API.** Un jeton personnel qui permet à un script ou une application d'appeler l'API de KolleK en votre nom. Voir @doc(apiKeys.manage).

**Webhook.** Une URL que vous enregistrez pour recevoir des notifications signées de KolleK. Aucun événement de l'application n'en déclenche encore. Voir @doc(webhooks.overview).

## Pour aller plus loin

- Toutes les options que ces termes peuvent prendre : @doc(reference.fieldAndStatus).
- Les concepts derrière les termes, expliqués en détail : @doc(coreConcepts.index).
