---
id: security.alertEmails
title: Emails d'alerte de connexion et de sécurité
slug: security-alert-emails
section: security
---

# Emails d'alerte de connexion et de sécurité

De temps en temps, KolleK peut vous envoyer un email sans que vous ayez rien demandé. Ces alertes existent pour garantir que, lorsque quelque chose se passe autour de votre utilisateur, vous en entendiez parler par KolleK avant de l'apprendre autrement. Cette page recense chaque alerte, ce qu'elle signifie, et ce qu'il faut faire si l'une d'elles vous surprend.

## Tentative de connexion échouée

**Quand elle arrive :** quelqu'un a saisi votre email avec un mauvais mot de passe sur la page de connexion.

**Si c'était vous**, en ayant fait une faute de frappe sur votre propre mot de passe, ignorez la.

**Si ce n'était pas vous**, quelqu'un tente votre adresse. Une tentative échouée isolée n'est généralement qu'un bruit de fond, mais des alertes répétées signifient que votre email est ciblé. Assurez vous que votre mot de passe est unique à KolleK, et activez l'@doc(security.twoFactorAuth, "authentification à deux facteurs") afin qu'un mot de passe deviné ne suffise pas.

## Nouvelle connexion

**Quand elle arrive :** une connexion réussie a eu lieu, et l'email nomme l'appareil d'où elle provient.

**Si c'était vous**, sur un nouveau navigateur, téléphone, ou ordinateur, ignorez la.

**Si ce n'était pas vous**, quelqu'un possède votre mot de passe. @doc(auth.resetPassword, "Changez votre mot de passe") immédiatement, et vérifiez votre compte à la recherche de quoi que ce soit d'inattendu.

## Changement d'adresse IP

**Quand elle arrive :** vous vous êtes connecté depuis une adresse réseau différente de la dernière fois.

C'est normal lorsque vous voyagez, changez de réseau, ou que votre fournisseur fait tourner ses adresses. Cela ne mérite votre attention que si cela survient en même temps qu'une connexion que vous ne reconnaissez pas.

## Clé API créée, clé API supprimée

**Quand elle arrive :** une @doc(apiKeys.manage, "clé API") a été créée ou révoquée sur votre utilisateur.

**Si c'était vous**, en gérant vos clés, ignorez la.

**Si ce n'était pas vous**, prenez la au sérieux. Une clé inattendue signifie que quelqu'un disposait d'un accès suffisant pour en créer une. Révoquez la clé, changez votre mot de passe, et vérifiez vos clés restantes ainsi que leur dernière date d'utilisation.

:::note
Les jetons de connexion créés lorsque vous vous connectez via l'API ne déclenchent pas l'email de création de clé. Seules les clés que vous créez manuellement le font, afin que l'alerte reste pertinente.
:::

## Emails que vous avez demandés

Deux autres emails n'arrivent que parce que quelqu'un les a demandés, ce ne sont donc pas des alertes en soi : l'email de @doc(auth.magicLinks, "lien magique"), et l'email de réinitialisation de mot de passe. Si vous en recevez un que vous n'avez pas demandé, quelqu'un a saisi votre adresse dans ce formulaire. Aucun des deux ne peut être utilisé sans accès à votre boîte de réception, mais des emails non sollicités répétés sont un autre signe que votre adresse est sondée.

## Si quelque chose semble vraiment anormal

1. @doc(auth.resetPassword, "Changez votre mot de passe").
2. Activez l'@doc(security.twoFactorAuth, "authentification à deux facteurs") si elle est désactivée.
3. Vérifiez vos @doc(apiKeys.manage, "clés API") et révoquez tout ce que vous ne reconnaissez pas.
4. Consultez @doc(activity.logAndSentEmails, "votre journal d'activité personnel") à la recherche d'actions que vous n'avez pas effectuées.

## Et ensuite

- Consultez tout ce que KolleK vous a déjà envoyé, avec le statut de livraison : @doc(activity.logAndSentEmails, "Votre journal d'activité personnel et vos emails envoyés").
- Le catalogue complet de chaque email que KolleK peut envoyer : @doc(reference.emailsSent).
