---
id: troubleshooting.emailDelivery
title: Dépannage de la livraison des emails
slug: troubleshoot-email-delivery
section: troubleshooting
---

# Dépannage de la livraison des emails

Vous avez invité quelqu'un et rien n'est arrivé. Vous avez demandé un lien magique et votre boîte de réception reste vide. Cette page explique pourquoi des emails attendus n'arrivent pas et comment découvrir ce qui s'est réellement passé.

## La cause la plus courante : une instance récente n'envoie pas d'email

Sur une instance auto hébergée récemment installée, le système d'envoi d'emails de KolleK se contente par défaut de **journaliser les emails plutôt que de les envoyer**. Chaque email est composé et enregistré, mais rien ne quitte le serveur tant qu'un opérateur n'a pas configuré un véritable service d'envoi.

C'est volontaire, afin qu'une instance non configurée n'échoue jamais silencieusement et n'envoie jamais de spam par accident. Mais cela signifie que sur une installation récente, les invitations, les liens magiques, les réinitialisations de mot de passe et les alertes de sécurité semblent tous disparaître.

:::note
Si personne n'a encore configuré l'envoi d'emails sur votre instance, aucun email n'arrivera, pour personne, jamais. C'est la première chose à vérifier.
:::

**Si vous administrez l'instance**, configurez SMTP ou Resend en suivant @doc(selfHosting.setupEmailDelivery).

**Si quelqu'un d'autre l'administre**, indiquez-lui cette page. Il n'y a rien que vous puissiez changer depuis l'application.

## Vérifier ce qui a vraiment été envoyé

KolleK enregistre chaque email qu'il vous envoie, avec son statut de livraison. Rendez-vous sur votre profil et ouvrez votre historique des **emails envoyés**. Chaque entrée indique quand l'email a été envoyé, et lorsque le suivi est disponible, s'il a été livré ou rejeté.

Comment interpréter ce que vous trouvez :

- **L'email est listé et marqué comme livré.** KolleK a fait son travail. Vérifiez votre dossier spam, et recherchez l'adresse de l'expéditeur dans votre boîte de réception.
- **L'email est listé et marqué comme rejeté.** Votre fournisseur d'email l'a refusé. Vérifiez que votre adresse est correcte dans votre profil, et si votre fournisseur bloque l'expéditeur de l'instance.
- **L'email est listé sans information de livraison.** Sur les instances qui envoient via SMTP simple, le suivi de livraison n'est pas disponible, c'est donc normal. L'absence de rejet est un bon signe.
- **L'email n'est pas listé du tout.** Il n'a jamais été composé, ce qui signifie généralement que l'action ne s'est pas terminée. Réessayez l'action.

Tous les détails sur cet écran dans @doc(activity.logAndSentEmails, "Votre journal d'activité personnel et vos emails envoyés").

## Une invitation n'est jamais parvenue à l'invité

L'email d'invitation est envoyé à l'invité, il n'apparaît donc jamais dans votre propre historique d'envoi. Demandez à l'invité de vérifier ses spams, vérifiez que vous avez bien saisi son adresse, et rappelez-vous que les invitations expirent après sept jours. En cas de doute, envoyez-en une nouvelle. Sur une instance récente, vérifiez d'abord la configuration de l'envoi d'emails, comme indiqué ci-dessus.

## Les vérifications, réinitialisations et liens magiques atterrissent dans les spams

Les emails transactionnels provenant d'une petite instance auto hébergée sont exactement le genre de message que les filtres anti-spam suspectent. Marquer un message comme « pas indésirable » aide généralement à corriger le comportement de votre fournisseur. Les opérateurs peuvent améliorer la délivrabilité avec une configuration d'expéditeur adaptée, couverte dans @doc(selfHosting.setupEmailDelivery).

## Pour aller plus loin

- Configuration opérateur pour un envoi réel : @doc(selfHosting.setupEmailDelivery).
- Votre historique d'emails personnel : @doc(activity.logAndSentEmails, "Votre journal d'activité personnel et vos emails envoyés").
- Ce qu'est chaque email et quand il se déclenche : @doc(reference.emailsSent).
