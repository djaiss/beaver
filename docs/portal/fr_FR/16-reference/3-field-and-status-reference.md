---
id: reference.fieldAndStatus
title: Référence des champs et statuts
slug: champs-et-statuts
section: reference
---

# Référence des champs et statuts

Chaque liste d'options que vous rencontrez dans un formulaire KolleK, réunie en un seul endroit facile à parcourir. Chaque groupe renvoie vers le guide qui l'utilise. Pour les définitions des termes eux mêmes, voir le @doc(reference.glossary, "glossaire").

## Statuts d'exemplaire

Défini sur chaque exemplaire que vous enregistrez. Utilisé dans @doc(copies.track).

| Statut | Signification |
| --- | --- |
| Possédé | Vous détenez cet exemplaire. La valeur par défaut pour un nouvel exemplaire. |
| Commandé | Acheté ou réservé, en route vers vous. |
| Prêté | Chez quelqu'un d'autre pour le moment. La garde a changé, pas la propriété. |
| Vendu | Vous l'avez vendu et ne le possédez plus. |
| Offert | Vous l'avez donné. |
| Perdu | Vous ne le retrouvez pas et ne vous attendez pas à le retrouver. |
| Volé | Il vous a été dérobé. |
| Mis au rebut | Jeté ou recyclé, avec une date de mise au rebut facultative. |
| Autre | Tout ce que la liste ci dessus ne couvre pas. |

:::note
Possédé, Commandé et Prêté comptent comme toujours détenus. Un exemplaire prêté reste le vôtre, il est simplement ailleurs.
:::

## Types de transaction

Défini sur chaque transaction. Utilisé dans @doc(copies.recordPaymentsAndValue). Les types marqués comme acquisition font entrer un exemplaire en votre possession, et la première transaction d'acquisition fournit la date d'acquisition de l'exemplaire.

| Type | Signification |
| --- | --- |
| Achat | Vous avez acheté l'exemplaire. Acquisition. |
| Vente | Vous avez vendu l'exemplaire. |
| Échange | Vous avez échangé quelque chose contre lui. Acquisition. |
| Don reçu | Quelqu'un vous l'a donné. Acquisition. |
| Don fait | Vous l'avez donné à quelqu'un. |
| Héritage | Il vous est parvenu par héritage. Acquisition. |
| Remboursement | Argent restitué sur une transaction précédente. |
| Frais | Un coût lié à l'exemplaire, tel que des frais de vente aux enchères. |
| Taxe | Une taxe payée sur l'exemplaire. |
| Livraison | Un coût de livraison enregistré séparément. |
| Autre | Tout événement financier que la liste ne couvre pas. |

## Types d'estimation et niveau de confiance

Défini sur chaque estimation. Utilisé dans @doc(copies.recordPaymentsAndValue).

| Type d'estimation | Signification |
| --- | --- |
| Estimation personnelle | Votre propre jugement de la valeur. |
| Expertise professionnelle | Une expertise formelle réalisée par un professionnel. |
| Estimation de marché | Dérivée de données de marché ou de ventes actuelles. |
| Valeur d'assurance | La valeur utilisée à des fins d'assurance. |
| Estimation d'enchère | Une estimation donnée par une maison de vente aux enchères. |
| Estimation automatisée | Produite par un service ou un outil de tarification. |
| Autre | Toute autre base pour la valeur. |

| Confiance | Signification |
| --- | --- |
| Faible | Une estimation approximative. |
| Moyenne | Raisonnablement fondée. |
| Élevée | Bien étayée, telle qu'une expertise professionnelle récente. |
| Inconnue | La confiance n'a pas été enregistrée. |

## Statuts d'assurance

Défini sur chaque enregistrement d'assurance. Utilisé dans @doc(copies.insure). Le type de couverture sur un enregistrement d'assurance est un texte libre, il n'a donc pas de liste d'options fixe.

| Statut | Signification |
| --- | --- |
| Active | La police couvre actuellement l'exemplaire. |
| Expirée | La période de couverture est terminée. |
| Annulée | La police a été annulée avant sa date de fin. |
| En attente | La couverture est organisée mais pas encore en vigueur. |

## Sens et statuts de prêt

Défini sur chaque prêt. Utilisé dans @doc(loans.lendAndBorrow).

| Sens | Signification |
| --- | --- |
| Prêté | Votre exemplaire a quitté vos mains, par exemple pour un ami ou une exposition. |
| Emprunté | La pièce de quelqu'un d'autre est entre vos mains. |

| Statut | Signification |
| --- | --- |
| Prévu | Convenu mais pas encore remis. |
| Actif | L'exemplaire est actuellement sorti (ou entré). |
| En retard | Toujours sorti au delà de sa date d'échéance. KolleK signale cela automatiquement chaque jour. |
| Retourné | Le prêt s'est terminé et l'exemplaire est revenu. |
| Annulé | Le prêt n'a jamais eu lieu. |
| Perdu | L'exemplaire n'est pas revenu. |

## Types d'entretien

Défini sur chaque enregistrement d'entretien. Utilisé dans @doc(copies.recordMaintenance).

| Type | Signification |
| --- | --- |
| Nettoyage | Nettoyage courant. |
| Réparation | Réparation d'un dommage. |
| Révision | Entretien périodique, tel que la révision d'une montre. |
| Conservation | Travail visant à stabiliser et préserver. |
| Restauration | Travail visant à ramener l'exemplaire à un état antérieur. |
| Remplacement | Remplacement d'une pièce ou d'un composant. |
| Inspection | Une vérification sans intervention. |

## Types d'événement de provenance et précision de date

Défini sur chaque événement de provenance. Utilisé dans @doc(copies.traceProvenance).

| Type d'événement | Signification |
| --- | --- |
| Acquisition | L'exemplaire est entré dans une collection. |
| Vente | L'exemplaire a été vendu. |
| Don | L'exemplaire a changé de mains sous forme de don. |
| Héritage | L'exemplaire est passé par une succession. |
| Transfert de propriété | La propriété a changé d'une autre façon. |
| Transfert de garde | L'exemplaire s'est déplacé sans changer de propriétaire. |
| Prêt | L'exemplaire est sorti en prêt. |
| Retour | L'exemplaire est revenu d'un prêt. |
| Exposition | L'exemplaire a été montré publiquement. |
| Authentification | L'exemplaire a été vérifié comme authentique. |
| Expertise | L'exemplaire a été formellement évalué. |
| Restauration importante | Un travail majeur qui a sa place dans l'histoire. |
| Origine | Où et quand l'exemplaire a été fabriqué. |
| Découverte | L'exemplaire a été trouvé ou redécouvert. |
| Autre | Tout autre chapitre de l'histoire. |

Les dates de provenance sont souvent incertaines, chaque événement porte donc une précision :

| Précision | Signification |
| --- | --- |
| Date exacte | La date complète est connue. |
| Mois | Connue au mois près. |
| Année | Connue à l'année près. |
| Approximative | Une meilleure estimation. À lire comme « vers ». |
| Inconnue | Aucune date n'est enregistrée. |

## Types de document

Défini sur chaque document. Utilisé dans @doc(copies.attachDocuments).

| Type | Signification |
| --- | --- |
| Reçu | Preuve d'un achat. |
| Facture | Une facture pour l'exemplaire ou un travail effectué sur lui. |
| Certificat | Un certificat fourni avec l'exemplaire. |
| Expertise | Une évaluation écrite. |
| Assurance | Documents de police d'assurance. |
| Photographie | Une photo conservée comme enregistrement plutôt que comme image de galerie. |
| Rapport d'état | Une évaluation écrite de l'état. |
| Rapport de restauration | Un enregistrement d'un travail de restauration. |
| Catalogue | Une entrée ou une fiche de catalogue. |
| Correspondance | Lettres ou emails à propos de l'exemplaire. |
| Preuve de propriété | Documents prouvant la propriété. |
| Preuve d'authenticité | Documents prouvant que l'exemplaire est authentique. |
| Autre | Tout ce qui vaut la peine d'être conservé. |

## Types de champ personnalisé

Choisi lors de la définition d'un champ personnalisé sur un type de collection. Utilisé dans @doc(collectionTypes.setup).

| Type de champ | Signification |
| --- | --- |
| Texte | Texte libre, tel qu'un auteur ou un éditeur. |
| Nombre | Une valeur numérique, telle qu'un numéro d'épisode. |
| Date | Une date de calendrier, telle qu'une date de sortie. |
| Oui / Non | Une case à cocher, telle que « Dédicacé ». |
| Sélection | Un choix parmi une liste d'options que vous définissez. |
| Note | Une notation par étoiles, jusqu'à cinq étoiles. |

## Visibilité de collection

Défini sur chaque collection. Utilisé dans @doc(collections.share). Le paramètre est enregistré dès aujourd'hui et sera appliqué une fois le partage disponible ; voir @doc(troubleshooting.featureStatus).

| Visibilité | Signification |
| --- | --- |
| Privée | Destinée à vous seul. |
| Partagée | Destinée à tout le monde dans votre compte. |
| Publique | Destinée à toute personne disposant du lien, en lecture seule, sans connexion. |

## Pour aller plus loin

- Ce que signifient les termes : @doc(reference.glossary).
- Les enregistrements sur lesquels vivent ces options : @doc(copyHistory.concept, "L'historique d'un exemplaire expliqué").
