---
id: kollek.howOrganized
title: Wie KolleK aufgebaut ist
slug: wie-kollek-aufgebaut-ist
section: kernkonzepte
---

# Wie KolleK aufgebaut ist

Diese Seite gibt dir die gesamte Landkarte, bevor es ins Detail geht. Alles andere in diesem Abschnitt zoomt in einen einzelnen Teil davon hinein.

## Das Rückgrat: vier Ebenen

Alles, was du in KolleK katalogisierst, lebt in einer einfachen Verschachtelung:

- Ein **@doc(accounts.usersAndRoles, "Konto")** ist dein Arbeitsbereich. Alles darunter gehört zu genau einem Konto.
  - Eine **@doc(collections.overview, "Sammlung")** ist eine benannte Gruppe von Dingen, wie "Meine Comics" oder "Weinkeller".
    - Ein **@doc(items.itemsVsCopies, "Objekt")** ist eine Art von Ding, wie "Amazing Spider-Man #1".
      - Ein **@doc(items.itemsVsCopies, "Exemplar")** ist eine physische Ausgabe dieses Objekts, die du tatsächlich besitzt.

Emmas Konto enthält ihre Sammlung "Meine Comics". Darin liegt das Objekt "Amazing Spider-Man #1". Sie besitzt zwei davon, also hat das Objekt zwei Exemplare, jedes mit eigenem Zustand, eigenem Aufbewahrungsort und eigenem Wert.

Die Trennung zwischen Objekt und Exemplar ist das Herzstück des Modells und bekommt @doc(items.itemsVsCopies, "eine eigene Seite"). Wenn du nur eine Konzeptseite liest, lies diese.

## Die geteilten Hilfsmittel

Um das Rückgrat herum stehen ein paar kontoweite Werkzeuge. Sie werden einmal definiert und überall wiederverwendet:

- **@doc(collectionTypes.overview)** entscheiden, welche Details jede Art von Objekt erfasst. Ein Comics-Typ fragt nach einer Ausgabennummer, ein Wein-Typ fragt nach einem Jahrgang.
- **@doc(organizing.categoriesSetsAndSeries)** gruppieren Objekte auf drei verschiedene Arten: Ablage innerhalb einer Sammlung, Verfolgung einer endlichen Liste bis zur Vollständigkeit und Verknüpfung eines Franchises über Sammlungen hinweg.
- **@doc(tags.overview)** sind frei formulierte Beschriftungen, die für das ganze Konto gelten, wie "Signiert".
- **@doc(locations.overview)** beschreiben, wo Exemplare physisch leben, und sie verschachteln sich: eine Kiste auf einem Regal in einem Zimmer.
- **@doc(conditions.overview)** bewerten den Zustand eines Exemplars, von Neu bis Beschädigt.

## Die Verlauf-Ebene

Jedes Exemplar trägt außerdem @doc(copyHistory.concept, "seine eigene Historie"): was du bezahlt hast, was es über die Zeit wert war, Versicherung, Leihgaben, Wartung, Herkunft und jeden Ort, an dem es aufbewahrt wurde. Das Exemplar zeigt seinen aktuellen Zustand, und die Verlaufseinträge erzählen die Geschichte dahinter.

## Den Überblick behalten

:::note
Beschreibende Details leben am Objekt. Alles Physische (Zustand, Standort, Geld, Historie) lebt am Exemplar. Im Zweifel fragst du dich: "Gilt das für jedes Exemplar dieses Dings, oder nur für dieses eine?"
:::

## Wie geht es weiter

- Lerne den Arbeitsbereich und die Personen darin kennen in @doc(accounts.usersAndRoles).
- Springe direkt zur Kernidee in @doc(items.itemsVsCopies).
- Lieber tun als lesen? Probiere den @doc(gettingStarted.quickStart, "Fünf-Minuten-Schnelleinstieg").
