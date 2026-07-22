---
id: tutorials.trackValuableItem
title: "Tutoriel : suivre la vie complète d'un objet de valeur"
slug: suivre-un-objet-de-valeur
section: tutoriels
---

# Tutoriel : suivre la vie complète d'un objet de valeur

La plupart des objets ont besoin d'un état, d'un emplacement, et peut être d'un prix. Un objet réellement précieux mérite davantage : la preuve de ce que vous avez payé, un avis professionnel sur sa valeur, une assurance, les documents pour étayer tout cela, et un enregistrement de tous les endroits où il va et de tout ce qui lui est fait. KolleK enregistre chacun de ces éléments comme sa propre entrée datée sur l'exemplaire, et ce tutoriel les met tous en œuvre sur une seule pièce.

Nous allons suivre Priya, qui vient d'acheter la meilleure montre de sa collection, un chronographe de 1968. À la fin, son exemplaire portera une transaction, une estimation, un enregistrement d'assurance, deux documents, un prêt terminé, un enregistrement d'entretien, et un récit de provenance, le tout lisible comme une seule chronologie.

C'est le tutoriel le plus long. Faites-le avec un objet réel qui vous appartient, ou lisez-le simplement pour voir comment les pièces s'assemblent.

## Avant de commencer

- Terminez d'abord @doc(tutorials.catalogueFirstCollection, "Cataloguer votre première collection de bout en bout"). Ce tutoriel suppose que la boucle centrale vous est devenue naturelle.
- Lisez @doc(copyHistory.concept, "L'historique d'un exemplaire expliqué"). C'est la carte pour tout ce qui suit.
- Souvenez-vous des deux règles qui gardent le modèle cohérent : l'argent ne vit toujours que dans les transactions, et réévaluer ou réassurer écrit un nouvel enregistrement au lieu d'écraser l'ancien.

## Étape 1 : cataloguer l'objet et son exemplaire

Priya crée l'objet « Heuer Carrera 2447 » dans sa collection Montres, qui utilise le type **Montres** prêt à l'emploi. Elle remplit les champs du type : **Marque**, **Modèle**, **Mouvement** (Automatique, Quartz, ou Manuel), et répond oui à **Boîte et papiers**.

Elle ajoute ensuite l'exemplaire, et un champ compte plus que d'habitude ici.

- **Identifiant.** Elle saisit le numéro de série de la montre. Pour les objets de valeur, c'est ce qui relie votre fiche à l'objet physique, de la même façon qu'un numéro de coque le fait pour un comic gradé.
- **État** et **emplacement**, comme toujours.

Tout ce qui suit se passe dans l'onglet **Historique** de cet exemplaire, qui affiche un exemplaire à la fois.

## Étape 2 : enregistrer l'acquisition

::::steps
:::step title="Ajouter la transaction d'achat"
Dans l'historique de l'exemplaire, ajoutez une **transaction** de type **Achat**. Priya saisit le montant, la maison de vente comme **contrepartie**, la **date**, la prime d'achat sous **frais**, et le numéro de lot comme **référence**.

::screenshot{label="Formulaire de transaction rempli pour un achat aux enchères"}
:::
::::

Pourquoi cela compte : cet unique enregistrement donne à l'exemplaire son prix payé et sa date d'acquisition, ancre les statistiques, et ancrera plus tard le récit de provenance. Faites-le correctement et tout le reste en dépend. Les détails se trouvent dans @doc(copies.recordPaymentsAndValue).

## Étape 3 : ajouter une estimation professionnelle

Priya fait expertiser la montre. Elle ajoute une **estimation** avec le type **Expertise professionnelle**, le montant expertisé, la confiance réglée sur **Élevée**, et le nom de l'expert comme évaluateur.

:::note
L'année prochaine, elle la fera expertiser à nouveau et ajoutera une nouvelle estimation. L'ancienne reste. La valeur estimée de l'exemplaire est toujours sa dernière estimation, et la séquence des estimations est ce qui vous permettra un jour de tracer sa valeur dans le temps.
:::

## Étape 4 : l'assurer

Avec une expertise professionnelle en main, l'assurance est l'étape suivante évidente. Priya ajoute un @doc(copies.insure, "enregistrement d'assurance") : l'**assureur**, la **valeur assurée**, le **numéro de police**, le **type de couverture**, la **franchise**, les **dates de début et de fin**, si c'est un **objet répertorié** sur la police, et les coordonnées de l'assureur. Elle laisse le statut sur **Actif**.

Quand la police sera renouvelée, elle ajoutera un nouvel enregistrement et marquera celui-ci **Expiré**. Les enregistrements expirés et annulés restent visibles, estompés, derrière l'actuel, ce qui est exactement ce dont on a besoin quand une réclamation demande quelle couverture existait une année donnée.

## Étape 5 : joindre les documents

Les enregistrements sont des affirmations. Les documents sont des preuves. Priya numérise deux papiers et les @doc(copies.attachDocuments, "joint") là où ils appartiennent.

::::steps
:::step title="Joindre le reçu à la transaction"
Sur la transaction d'achat, elle joint la facture de la vente aux enchères comme document de type **Reçu**, avec sa date d'émission et son numéro de facture comme référence.
:::

:::step title="Joindre l'expertise à l'estimation"
Sur l'estimation, elle joint le rapport de l'expert comme document de type **Expertise**.
:::
::::

Un document peut être un fichier téléversé (PDF, images, Word, Excel, CSV, ou texte brut, jusqu'à 20 Mo) ou un lien externe si les papiers se trouvent ailleurs. Joindre chaque document à l'enregistrement qu'il prouve, plutôt que vaguement à l'exemplaire, est ce qui rend l'histoire vérifiable plus tard.

## Étape 6 : la prêter pour une exposition, et la récupérer

Une société d'horlogerie locale demande à exposer la montre pendant un mois. La garde est exactement ce que suivent les @doc(loans.lendAndBorrow, "prêts").

::::steps
:::step title="Enregistrer le prêt sortant"
Priya crée un **prêt** avec la direction **Prêté**, la société comme partie, « Exposition » comme motif, les dates de prêt et d'échéance, et l'état de la montre au moment où elle quitte ses mains.
:::

:::step title="Voir le statut de l'exemplaire changer"
Tant que le prêt est ouvert, l'exemplaire se lit comme prêté. Il reste le sien, la garde a changé, pas la propriété. Si la date d'échéance était dépassée sans retour, KolleK signalerait automatiquement le prêt en retard.
:::

:::step title="Enregistrer le retour"
Quand la montre revient, elle enregistre le **retour**, qui capture la date de retour et l'état dans lequel elle est revenue. Comparer l'état de sortie et l'état d'entrée est ce qui rend un dommage de transport visible plutôt que discutable.
:::
::::

## Étape 7 : consigner l'entretien

Avant que la montre parte en exposition, Priya l'avait fait entretenir. Elle ajoute un @doc(copies.recordMaintenance, "enregistrement d'entretien") de type **Révision** : un titre, l'horloger qui l'a effectué, la date, le coût, l'état avant et après, et une **prochaine échéance** dans cinq ans afin que l'application puisse faire remonter le prochain entretien à son approche. Comme une révision complète sur un mouvement vintage est significative, elle choisit de l'inclure dans la provenance de l'exemplaire.

## Étape 8 : construire le récit de provenance

Enfin, l'histoire de la propriété. Priya connaît le passé de la montre grâce au catalogue de la vente aux enchères, et elle l'enregistre comme des @doc(copies.traceProvenance, "événements de provenance"), du plus ancien au plus récent.

- Un événement d'**Origine** pour sa fabrication, daté de l'année 1968.
- Un **Transfert de propriété** à la famille du propriétaire d'origine, avec la précision de date réglée sur **Approximative**, car le catalogue indique seulement « vers 1975 ».
- Un événement d'**Exposition** pour la présentation de la société qu'elle vient de terminer.
- Sa propre **Acquisition**, datée exactement, liée à la transaction d'achat de l'étape 2.

Deux choses à remarquer. La précision de date existe parce que la provenance est souvent incertaine, un événement peut être daté exactement, au mois, à l'année, approximativement, ou laissé inconnu, et il s'affiche en conséquence. Et les événements de provenance ne portent aucun montant : un événement lié à un achat ou une vente renvoie à sa transaction, de sorte que l'argent reste à un seul endroit.

## Étape 9 : lire l'histoire complète

Ouvrez la **chronologie** de l'exemplaire. Tout ce que vous venez d'enregistrer, l'achat, l'estimation, l'assurance, les documents, le prêt sortant et le retour, l'entretien, et les événements de provenance, se lit comme une seule histoire chronologique. La vue par défaut s'en tient aux entrées significatives, et la vue complète ajoute les entrées de routine. @doc(copyHistory.readTimeline) explique la vue en détail.

C'est là le résultat : un seul écran qui répond à ce que la montre a coûté, ce qu'elle vaut, qui l'a détenue, ce qui lui a été fait, et ce qui prouve tout cela.

## Erreurs courantes à éviter

- **Enregistrer le prix d'achat comme une estimation.** C'est une transaction. Cette distinction est l'ossature de tout le modèle.
- **Modifier d'anciens enregistrements au lieu d'en ajouter de nouveaux.** Une nouvelle expertise est une nouvelle estimation, une police renouvelée est un nouvel enregistrement d'assurance. L'historique ne fonctionne que s'il s'accumule.
- **Laisser des documents sans les joindre.** Un reçu classé sur la transaction qu'il prouve est une preuve. Un fichier vaguement joint à l'exemplaire est une numérisation que vous devrez réidentifier plus tard.

## Et ensuite

- Chaque type d'enregistrement utilisé ici a son propre guide détaillé dans la @doc(copyHistory.index, "section historique de l'exemplaire").
- Voyez comment ces enregistrements alimentent les chiffres dans @doc(insights.collectionStatistics).
- Vous partagez la collection avec d'autres ? @doc(tutorials.inviteHousehold, "Inviter votre foyer ou club").
