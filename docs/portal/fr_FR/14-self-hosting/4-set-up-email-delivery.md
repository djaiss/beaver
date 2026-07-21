---
id: selfHosting.setupEmailDelivery
title: Configurer la livraison des e-mails
slug: livraison-des-emails
section: auto-hebergement
---

# Configurer la livraison des e-mails

L'e-mail est le moyen par lequel KolleK atteint les personnes en dehors d'une session de navigateur : les @doc(collaboration.invitePeople, "invitations"), les @doc(auth.magicLinks, "liens magiques"), les réinitialisations de mot de passe, la vérification d'e-mail et les @doc(security.alertEmails, "alertes de sécurité") arrivent tous par e-mail. Tant que vous n'avez pas configuré la livraison, aucun d'entre eux ne va nulle part.

## Le comportement par défaut n'envoie rien

Une instance fraîchement installée est livrée avec `MAIL_MAILER=log`. Chaque e-mail est écrit dans le fichier de journal de l'application au lieu d'être envoyé. C'est délibéré : cela signifie qu'une instance à moitié configurée n'envoie jamais silencieusement du courrier depuis une mauvaise adresse, et vous pouvez lire exactement ce qui aurait été envoyé pendant les tests.

:::note
Si quelqu'un dit « je n'ai jamais reçu l'invitation » sur une instance récente, ce comportement par défaut en est presque toujours la cause. L'e-mail existe, dans le fichier de journal. Voyez @doc(troubleshooting.emailDelivery).
:::

Vous disposez de deux méthodes prises en charge pour envoyer de vrais e-mails : n'importe quel serveur SMTP, ou le service Resend.

## Option 1 : SMTP

::::steps
:::step title="Définissez le mailer et les détails du serveur"
Dans `.env`, définissez :

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

Tout fournisseur d'e-mail transactionnel ou serveur de messagerie autogéré disposant d'identifiants SMTP fonctionne.
:::

:::step title="Définissez l'identité de l'expéditeur"
Définissez l'adresse et le nom que vos utilisateurs verront :

```bash
MAIL_FROM_ADDRESS=kollek@yourdomain.com
MAIL_FROM_NAME="KolleK"
```

Utilisez un domaine que vous contrôlez et que vous avez configuré pour l'envoi (SPF et DKIM chez votre fournisseur), sinon votre courrier atterrira dans les indésirables.
:::

:::step title="Appliquez et testez"
Recréez les conteneurs, puis déclenchez un véritable e-mail, par exemple en demandant un lien magique depuis la page de connexion :

```bash
docker compose up -d
```
:::
::::

## Option 2 : Resend

Si vous utilisez [Resend](https://resend.com), définissez :

```bash
USE_RESEND=true
RESEND_API_KEY=re_your_api_key
```

Les e-mails sont alors envoyés via l'API de Resend plutôt que par SMTP, et chaque envoi enregistre l'identifiant de message Resend en même temps.

## Vérifier que la livraison fonctionne

KolleK enregistre chaque e-mail qu'il envoie, par utilisateur, avec son objet, son corps et son statut de livraison. Après votre test, vérifiez deux endroits :

- Votre boîte de réception, pour la raison évidente.
- La page **e-mails envoyés** du destinataire dans son profil, qui répertorie ce que l'instance lui a envoyé. Voyez @doc(activity.logAndSentEmails, "Votre journal d'activité personnel et vos e-mails envoyés").

Signes de défaillance courants :

- **Rien n'arrive et rien ne signale d'erreur.** Le mailer est toujours `log`. Vérifiez que `.env` a été appliqué en recréant les conteneurs.
- **Les e-mails partent mais atterrissent dans les indésirables.** Le domaine d'expédition n'est pas authentifié. Configurez SPF et DKIM chez votre fournisseur.
- **Des erreurs d'envoi apparaissent dans le journal.** Les identifiants ou les détails de l'hôte sont incorrects. Les journaux du worker de la file d'attente contiennent le message d'erreur du fournisseur.

Les e-mails sont envoyés par la file d'attente en arrière-plan, le conteneur **queue** doit donc être en fonctionnement pour que quoi que ce soit quitte l'instance.

## Et ensuite

- Reconnaissez les e-mails que votre instance envoie dans @doc(reference.emailsSent).
- Diagnostiquez les problèmes de livraison dans @doc(troubleshooting.emailDelivery).
