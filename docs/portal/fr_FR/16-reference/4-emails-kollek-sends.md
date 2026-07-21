---
id: reference.emailsSent
title: Emails envoyés par KolleK
slug: emails-kollek-sends
section: reference
---

# Emails envoyés par KolleK

Chaque email que le système peut envoyer, ce qui le déclenche, et qui le reçoit. Utilisez cette page pour reconnaître un message légitime, ou pour vérifier la livraison lorsque vous exploitez une instance.

KolleK conserve un enregistrement de chaque email qu'il vous envoie, y compris le statut de livraison et de rejet, sur votre @doc(activity.logAndSentEmails, "page des emails envoyés"). Les opérateurs qui n'ont pas encore configuré de service d'envoi d'emails devraient lire @doc(selfHosting.setupEmailDelivery), car une instance fraîchement installée se contente d'enregistrer les emails sans en envoyer aucun.

## Se connecter et rester connecté

| Email | Déclenché quand | Envoyé à |
| --- | --- | --- |
| Invitation au compte | Un propriétaire invite quelqu'un dans le compte. Le lien d'invitation expire au bout de sept jours. | L'adresse invitée |
| Lien magique | Quelqu'un demande un lien de connexion sans mot de passe. Le lien est valide cinq minutes. | L'email du compte |
| Vérification d'email | Vous vous inscrivez, ou vous changez votre adresse email. | La nouvelle adresse |
| Réinitialisation de mot de passe | Vous utilisez le lien mot de passe oublié. Le lien de réinitialisation est valide 60 minutes. | L'email du compte |

## Alertes de sécurité

Elles arrivent sans avoir été sollicitées lorsque quelque chose de notable se produit sur votre compte. Voir @doc(security.alertEmails) pour savoir quoi faire quand l'une d'elles vous surprend.

| Email | Déclenché quand | Envoyé à |
| --- | --- | --- |
| Alerte d'échec de connexion | Une tentative de connexion par mot de passe échoue sur un compte existant. | L'email du compte |
| Alerte de nouvelle connexion | Une connexion réussie se produit, en nommant l'appareil utilisé. | L'email du compte |
| Alerte de changement d'adresse IP | Une connexion provient d'une adresse IP différente de la dernière fois. | L'email du compte |
| Clé API créée | Vous créez une clé API manuellement. Les jetons créés en se connectant via l'API ne déclenchent pas cet avis. | L'email du compte |
| Clé API supprimée | Vous supprimez une clé API. | L'email du compte |

## Avis à l'opérateur

Ces emails vont à l'adresse de l'opérateur configurée sur l'instance, pas aux collectionneurs. Ils existent pour que la personne qui exploite le serveur sache quand des personnes partent.

| Email | Déclenché quand | Envoyé à |
| --- | --- | --- |
| Utilisateur supprimé | Une personne supprime son propre utilisateur, y compris la raison qu'elle a donnée. | L'adresse de l'opérateur |
| Utilisateur automatiquement supprimé | Le système supprime un utilisateur qui a opté pour la suppression pour inactivité et qui est inactif depuis six mois. | L'adresse de l'opérateur |

## Pour aller plus loin

- Reconnaître les alertes et y réagir : @doc(security.alertEmails).
- Faire réellement envoyer les emails sur votre instance : @doc(selfHosting.setupEmailDelivery).
- Vérifier ce qui vous a été envoyé : @doc(activity.logAndSentEmails, "Votre journal d'activité personnel et vos emails envoyés").
