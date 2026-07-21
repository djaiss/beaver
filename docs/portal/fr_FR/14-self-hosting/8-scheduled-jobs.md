---
id: selfHosting.scheduledJobs
title: Tâches de maintenance planifiées
slug: scheduled-jobs
section: self-hosting
---

# Tâches de maintenance planifiées

Chaque nuit, votre instance fait le ménage toute seule. Cette page vous indique ce qui s'exécute, quand, et ce qui doit être vrai pour que cela se produise, afin que rien de ce que l'application fait de son propre chef ne vous surprenne jamais.

## Les tâches nocturnes

Trois tâches s'exécutent quotidiennement, chacune mise en file d'attente sur la file de priorité basse :

- **00h30, suppression des utilisateurs inactifs.** Supprime les utilisateurs qui ont personnellement choisi la @doc(users.inactiveDeletion, "suppression automatique après inactivité") et qui sont inactifs depuis six mois ou plus. Chaque suppression est signalée à l'adresse définie dans `ACCOUNT_DELETION_NOTIFICATION_EMAIL`. Les utilisateurs qui n'ont jamais activé cette option ne sont jamais touchés.
- **01h00, purge de la corbeille.** Supprime définitivement tout ce qui se trouve dans la @doc(dataSafety.restoreFromTrash, "corbeille") depuis plus longtemps que la période de rétention (`TRASH_RETENTION_DAYS`, 30 jours par défaut). Dans cette fenêtre, les objets mis à la corbeille restent restaurables.
- **02h00, signalement des prêts en retard.** Marque les @doc(loans.lendAndBorrow, "prêts") actifs dont la date d'échéance est dépassée comme en retard, afin que les collectionneurs voient d'un coup d'œil ce qui n'est pas revenu.

Les trois sont sûres et attendues. Elles n'agissent que sur des éléments que les utilisateurs ont explicitement supprimés, choisis ou datés.

## Ce qui doit être en fonctionnement

Deux conteneurs rendent cela possible :

- Le rôle **scheduler** décide qu'il est temps et met chaque tâche en file d'attente.
- Le rôle **queue** les exécute réellement.

:::note
Si l'un des deux conteneurs est arrêté, la maintenance s'interrompt silencieusement : la corbeille s'accumule au-delà de sa date de rétention, les prêts en retard restent marqués comme actifs, et les utilisateurs inactifs ayant opté pour la suppression ne sont pas nettoyés. Rien ne casse, mais rien ne s'exécute non plus. Vérifiez `docker compose ps` si le comportement nocturne semble s'être arrêté.
:::

Tout rattrape son retard à la prochaine exécution réussie ; une nuit manquée n'est pas un problème.

## Et ensuite

- Ajustez la fenêtre de rétention dans @doc(selfHosting.configure).
- Découvrez ce que les utilisateurs vivent de l'autre côté dans @doc(dataSafety.restoreFromTrash).
