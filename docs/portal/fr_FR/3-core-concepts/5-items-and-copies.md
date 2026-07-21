---
id: items.itemsVsCopies
title: Objets et exemplaires
slug: objets-et-exemplaires
section: concepts-fondamentaux
---

# Objets et exemplaires

C'est la page la plus importante de la documentation. La différence entre un objet et un exemplaire est l'idée qui distingue KolleK d'une simple liste, et presque toutes les autres pages supposent que vous la connaissez. Elle prend deux minutes à apprendre.

## La distinction

Un **objet** est le *type de chose*. Un **exemplaire** est *une instance physique que vous possédez réellement*.

« Amazing Spider-Man #1 » est un objet. Celui légèrement usé dans la boîte à comics d'Emma est un exemplaire. Celui quasiment neuf qu'elle a acheté aux enchères en est un autre. Même objet, deux exemplaires.

- Vous possédez trois fois le même comics ? C'est **un seul objet avec trois exemplaires**.
- Chaque exemplaire a son propre @doc(conditions.overview, "état"), son propre @doc(locations.overview, "emplacement") de stockage, sa propre valeur, et son propre @doc(copyHistory.concept, "historique").
- L'objet porte tout ce que les exemplaires ont en commun : le nom, la description, les photos, les valeurs des champs personnalisés, les étiquettes.

## La règle à retenir

Les détails descriptifs et de classification vivent sur l'**objet**. Tout ce qui concerne l'état, l'emplacement, l'argent et l'historique vit sur l'**exemplaire**.

Demandez-vous : « est-ce vrai pour n'importe quel exemplaire de cette chose ? » L'auteur du comics est le même pour chaque exemplaire, donc cela appartient à l'objet. Ce que vous avez payé diffère pour chacun, donc cela appartient à l'exemplaire.

## Un exemple concret

Priya cataloge une Omega Speedmaster de 1968.

- L'**objet** porte le nom, une description, des photos, et des champs personnalisés tels que Marque, Modèle et Mouvement.
- Son premier **exemplaire** est évalué Usé, vit dans sa vitrine, et porte le prix qu'elle a payé en 2019 ainsi qu'une évaluation professionnelle.
- Son second **exemplaire**, hérité de son grand-père, est évalué Abîmé, vit dans un coffre-fort, et porte un enregistrement d'assurance ainsi qu'une trace de provenance remontant à 1970.

Une montre en tant que concept, deux montres physiques bien différentes, chacune entièrement suivie.

## Ce qu'un exemplaire enregistre

Au-delà de l'état et de l'emplacement, un exemplaire porte un identifiant facultatif (un numéro de série ou de coque), un statut, une quantité, une note, et une valeur estimée. Le statut couvre toute la vie d'un exemplaire : Possédé, Commandé, Prêté, Vendu, Offert, Perdu, Volé, Cédé, ou Autre. Les détails se trouvent dans @doc(copies.track).

Ce que vous avez payé et ce que vaut un exemplaire ne sont pas saisis directement sur l'exemplaire. Ils proviennent de ses transactions et de ses évaluations, qui font partie de @doc(copyHistory.concept, "l'historique d'un exemplaire").

## L'erreur à éviter

:::note
Deux exemplaires de la même chose sont deux exemplaires d'un objet, jamais deux objets. Si vous êtes sur le point de créer « Amazing Spider-Man #1 (le deuxième) », arrêtez-vous et ajoutez plutôt un exemplaire à l'objet existant.
:::

Les objets dupliqués fragmentent votre historique et vos statistiques. Un seul objet avec plusieurs exemplaires garde le catalogue net et permet à chaque pièce physique de raconter sa propre histoire.

## Et ensuite

- Enregistrez vos exemplaires dans @doc(copies.track).
- Découvrez ce qu'un exemplaire peut mémoriser dans @doc(copyHistory.concept).
- Capturez l'argent correctement dans @doc(copies.recordPaymentsAndValue).
