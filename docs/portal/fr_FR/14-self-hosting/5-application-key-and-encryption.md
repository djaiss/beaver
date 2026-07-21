---
id: selfHosting.applicationKeyAndEncryption
title: La clé d'application et le chiffrement
slug: application-key-and-encryption
section: self-hosting
---

# La clé d'application et le chiffrement

Cette page explique la règle opérationnelle la plus importante de la gestion de KolleK. Tout le reste concernant l'instance est récupérable avec de la patience. C'est le seul réglage capable de détruire des données de façon irréversible.

## Ce que fait la clé

KolleK chiffre les champs sensibles au repos avec la clé d'application de l'instance, la valeur `APP_KEY` de votre `.env`. Noms, détails des objets, valeurs de champs personnalisés, noms de fichiers, enregistrements d'e-mails, secrets de webhooks : une trentaine de modèles environ comportent des colonnes chiffrées. Ce qui atterrit dans la base de données pour ces champs est du texte chiffré, illisible sans la clé. Cette même clé protège aussi les sessions utilisateur.

C'est ce que décrit @doc(dataSafety.howProtected) du point de vue de l'utilisateur. Sur le plan opérationnel, cela signifie que la clé n'est pas un simple détail de configuration. Elle constitue la moitié de vos données.

## La règle

:::warning
Définissez la clé d'application une seule fois, avant le premier démarrage, et ne la changez jamais sur une instance en fonctionnement. Si la clé est perdue ou modifiée, chaque colonne chiffrée et chaque session devient définitivement illisible. Il n'existe aucune récupération, aucun support, aucun outil capable de ramener les données.
:::

Trois conséquences pratiques :

- **Sauvegardez la clé avec les données.** Une sauvegarde de base de données sans la clé correspondante se restaure en texte chiffré illisible. Conservez la clé dans un gestionnaire de mots de passe ou un coffre de secrets, séparément du serveur.
- **Gardez-la identique partout.** Les trois conteneurs applicatifs (web, queue, scheduler) doivent tourner avec la même clé. Le fichier Compose fourni partage un seul `.env`, ce qui gère ce point. Préservez cette propriété dans tout déploiement personnalisé.
- **Ne la régénérez pas « par précaution ».** Exécuter `key:generate` sur une instance en fonctionnement est le désastre auto-infligé classique. L'instance refuse de démarrer sans clé précisément pour qu'aucune instance ne se retrouve accidentellement sans clé et ne génère une nouvelle clé en cours de vie.

## Faire tourner la clé délibérément

Certains opérateurs doivent faire tourner leurs clés selon un calendrier pour des raisons de politique interne. KolleK prend cela en charge grâce aux clés précédentes : la `APP_KEY` actuelle chiffre tout ce qui est nouveau, tandis que les clés listées dans `APP_PREVIOUS_KEYS` (séparées par des virgules) peuvent encore déchiffrer les données existantes.

```bash
APP_KEY=base64:NEW_KEY_HERE
APP_PREVIOUS_KEYS=base64:OLD_KEY_HERE
```

Générez une nouvelle clé avec `php artisan key:generate --show` (jamais `key:generate` seul, qui écrase votre clé en fonctionnement), déplacez l'ancienne clé dans `APP_PREVIOUS_KEYS`, définissez la nouvelle comme `APP_KEY`, puis recréez les conteneurs.

:::warning
Ne retirez jamais une clé de `APP_PREVIOUS_KEYS` tant que des données qu'elle a chiffrées existent encore. Les données ne sont rechiffrées avec la nouvelle clé que lorsqu'elles sont écrites à nouveau, si bien que d'anciens enregistrements peuvent dépendre de l'ancienne clé indéfiniment.
:::

Si la rotation ne vous est pas imposée, la politique sûre la plus simple est : une seule clé, définie une fois, bien sauvegardée.

## Et ensuite

- Assurez-vous que la clé fait partie de votre @doc(selfHosting.backupAndRestore, "plan de sauvegarde et de restauration").
- Lisez le point de vue de l'utilisateur sur le chiffrement dans @doc(dataSafety.howProtected).
