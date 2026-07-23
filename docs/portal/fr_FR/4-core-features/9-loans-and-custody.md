---
id: loans.custody
title: Prêts et garde
slug: prets-et-garde
section: fonctionnalites
---

# Prêts et garde

Un prêt est un transfert temporaire de garde sans aucun transfert de propriété. Lorsque vous prêtez une pièce à un ami, à une galerie ou à un musée, elle reste la vôtre. Lorsque vous empruntez une pièce, elle appartient toujours à quelqu'un d'autre. La section **Prêts** est la vue à l'échelle du compte de tout ce qui est actuellement hors de vos mains ou entre vos mains à titre de prêt.

La lecture de la section est ouverte à tous les rôles. Enregistrer, retourner, modifier et supprimer un prêt nécessite le rôle **éditeur** ou **propriétaire**.

## Les deux directions

Chaque prêt va dans l'un des deux sens, et la section affiche une direction à la fois. Utilisez le sélecteur en haut pour passer de l'une à l'autre.

- **Prêté.** Une pièce qui vous appartient et que quelqu'un d'autre détient. Tant qu'un prêt sortant est actif ou en retard, son exemplaire apparaît comme **Prêté** dans votre collection, car il n'est pas physiquement avec vous.
- **Emprunté.** Une pièce qui appartient à quelqu'un d'autre et que vous détenez pour le moment. Une pièce empruntée ne change jamais l'affichage de vos propres exemplaires, car vous ne l'avez jamais possédée.

## Ce que montrent les onglets

Au sein d'une direction, les onglets découpent les mêmes prêts de différentes façons.

- **Tous les prêts.** Chaque prêt dans la direction, avec une zone de recherche et des filtres par collection, statut et ordre de tri.
- **Échéances et retards.** Trois listes : les prêts dont la date d'échéance est dépassée, les prêts arrivant à échéance dans les trente jours, et les prêts sans terme qui n'ont aucune date d'échéance.
- **Risques et exceptions.** Les prêts qui méritent un second regard : en retard, perdus, retournés en moins bon état, sans date d'échéance, sans état enregistré au départ, ou prêtés sans aucun document au dossier.
- **Par partie.** Une fiche par personne ou institution, afin que vous puissiez voir d'un coup tout ce qu'un même emprunteur ou prêteur détient.
- **Dépôts.** Ce que vous détenez ou ce qui vous est dû sur l'ensemble des prêts en cours, et les prêts qui comportent un dépôt.
- **Chronologie.** Les échéances à venir, les pièces récemment retournées et les pièces récemment prêtées.

Les tuiles de statistiques en haut sont des raccourcis : chacune ouvre l'onglet qui y répond.

## Enregistrer un prêt

Vous pouvez démarrer un prêt directement depuis la section, sans avoir à chercher l'exemplaire au préalable.

::::steps
:::step title="Ouvrir le panneau de nouveau prêt"
Choisissez **Nouveau prêt**. Sélectionnez la direction, puis descendez de la collection à l'objet, jusqu'à l'exemplaire précis qui se déplace.
:::

:::step title="Nommer la partie et les dates"
Indiquez à qui la pièce est destinée ou de qui elle provient, la date de départ et une date d'échéance. Cochez **sans terme** lorsqu'aucune date de retour n'a été convenue.
:::

:::step title="Enregistrer l'état et un éventuel dépôt"
Choisissez l'**état au départ** afin qu'un retour ultérieur puisse y être comparé, et enregistrez un **dépôt** si un montant a changé de mains. La devise du dépôt reprend par défaut celle de la collection.
:::

:::step title="Marquer le prêt pour la provenance s'il fait partie de l'histoire"
Cochez **inclure dans la provenance** pour un prêt institutionnel ou une exposition, et un événement de provenance correspondant est généré. Laissez la case décochée pour un prêt personnel informel, qui reste uniquement dans l'historique des prêts.
:::
::::

### Un seul prêt en cours par exemplaire

Un exemplaire physique ne peut se trouver qu'à un seul endroit à la fois, donc un exemplaire peut avoir au plus un prêt **sortant en cours**. Si vous essayez de prêter un exemplaire déjà sorti, la section le bloque et vous demande de retourner le prêt en cours d'abord. Cette règle s'applique aussi dans l'API JSON.

## Retourner un prêt

Clôturer un prêt est une étape à part entière, pas une modification, afin de capturer ce qu'une modification ne saurait retenir.

::::steps
:::step title="Ouvrir le prêt et le marquer comme retourné"
Ouvrez le prêt depuis n'importe quelle liste, puis choisissez **Marquer comme retourné**.
:::

:::step title="Enregistrer le retour"
Indiquez la date à laquelle la pièce est revenue et l'**état au retour**. Définir un état au retour met à jour l'état actuel de l'exemplaire et ramène l'exemplaire sous votre garde.
:::
::::

Lorsque l'état au retour est moins bon que l'état au départ, le prêt est signalé comme dommage possible, à la fois sur le prêt lui même et dans la liste de risques **Retournés en moins bon état**.

## Exporter ce qui est sorti

Le bouton **Exporter ce qui est sorti** télécharge un fichier CSV des prêts en cours dans la direction actuelle, afin que vous disposiez d'une liste claire de ce qui se trouve actuellement entre les mains de quelqu'un d'autre, ou entre les vôtres.

## En rapport

- Les prêts apparaissent aussi dans l'historique propre à un exemplaire. Voir @doc(copies.track) pour l'enregistrement de l'exemplaire auquel ils se rattachent.
