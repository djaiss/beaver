---
id: activity.logAndSentEmails
title: Votre journal d'activité et vos emails envoyés
slug: activity-log-and-sent-emails
section: account-and-profile
---

# Votre journal d'activité et vos emails envoyés

KolleK conserve deux registres vous concernant que vous pouvez consulter à tout moment : tout ce que vous avez fait, et chaque email que le système vous a envoyé. Les deux se trouvent dans votre espace de profil, et les deux existent pour la même raison, la transparence. Quand vous vous demandez "ai je vraiment changé cela" ou "cet email de lien magique est il vraiment parti", la réponse est ici.

## Votre journal d'activité

Le @doc(activity.feedAndAuditTrail, "journal d'activité") qui parcourt l'ensemble du compte dispose d'une vue personnelle : un historique complet de vos propres actions, de la création d'un objet à la modification d'un paramètre. Ouvrez le depuis votre espace de profil.

Utilisez le pour retracer vos pas. Si l'emplacement d'un exemplaire semble incorrect, votre journal vous montrera si vous l'avez déplacé, et quand.

## Vos emails envoyés

KolleK enregistre chaque email qu'il vous envoie : liens magiques, invitations que vous avez reçues, messages de vérification et @doc(security.alertEmails, "alertes de sécurité"). Votre espace de profil les liste, du plus récent au plus ancien, dix par page.

Chaque entrée indique ce qui a été envoyé et quand. Lorsque le service de messagerie de l'instance renvoie l'information, vous verrez aussi si le message a été délivré, ou s'il a été rejeté.

Cette liste est le moyen le plus rapide de résoudre un problème d'email manquant :

- **L'email apparaît ici mais n'a jamais atteint votre boîte de réception.** Vérifiez votre dossier de courrier indésirable, et vérifiez si l'entrée indique un rejet.
- **L'email n'apparaît pas du tout ici.** L'action qui aurait dû le déclencher n'a pas eu lieu, redemandez le donc.
- **Des emails apparaissent ici mais aucun n'est jamais délivré.** Sur une instance auto hébergée, cela signifie généralement que la livraison des emails n'est pas encore configurée. Orientez votre administrateur vers @doc(selfHosting.setupEmailDelivery, "la configuration de la livraison des emails").

:::note
Cette page affiche les emails qui vous ont été envoyés. Elle est personnelle, comme le reste de votre profil, et les autres membres ne peuvent pas parcourir votre liste.
:::

## Et ensuite

- Comprenez l'historique à l'échelle du compte dans @doc(activity.feedAndAuditTrail).
- Il vous manque un email attendu ? Suivez @doc(troubleshooting.emailDelivery).
