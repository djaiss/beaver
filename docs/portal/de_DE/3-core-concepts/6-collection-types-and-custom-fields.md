---
id: collectionTypes.overview
title: Sammlungstypen und benutzerdefinierte Felder
slug: sammlungstypen-und-benutzerdefinierte-felder
section: kernkonzepte
---

# Sammlungstypen und benutzerdefinierte Felder

Comics brauchen eine Ausgabennummer. Wein braucht einen Jahrgang. Uhren brauchen ein Werk. KolleK rät nicht, was du sammelst, es lässt dich es definieren. Diese Seite erklärt die Bausteine: Typen, benutzerdefinierte Felder und Feldgruppen.

## Sammlungstypen

Ein **Sammlungstyp** beschreibt eine Art von Ding, das du sammelst: Comics, Schallplatten, Wein. Er ist der Container für die benutzerdefinierten Felder, die für diese Art von Ding Sinn ergeben.

Typen gelten kontoweit und sind wiederverwendbar. Definiere einen Comics-Typ einmal, und jede @doc(collections.overview, "Sammlung") in deinem Konto kann ihn aktivieren. Eine Sammlung kann mehrere Typen gleichzeitig aktivieren, was gemischten Sammlungen entgegenkommt: Noahs Sammlung "Musik" aktiviert sowohl Schallplatten als auch CD, sodass jedes Objekt als das eine oder das andere katalogisiert werden kann.

Bekommt ein Objekt einen Typ, wächst sein Formular um die benutzerdefinierten Felder, die dieser Typ definiert.

## Benutzerdefinierte Felder

Ein **benutzerdefiniertes Feld** ist ein Detail, nach dem ein Typ fragt. Jedes Feld hat einen eigenen Typ:

- **Text**, für alles Freiformulierte, wie Verlag oder Künstler.
- **Zahl**, für Ausgabe # oder Erscheinungsjahr.
- **Datum**, für ein Coverdatum.
- **Ja / Nein**, für Signiert oder Erstauflage.
- **Auswahl**, ein Dropdown mit von dir definierten Optionen, wie eine Bewertung von PSA 10, PSA 9 oder Roh.
- **Bewertung**, bis zu fünf Sterne, für deine persönliche "Meine Bewertung".

Die Werte werden pro Objekt erfasst. Emmas "Amazing Spider-Man #1" hat Ausgabe # 1 und Verlag Marvel; ihre anderen Comics teilen sich dieselben Felder mit ihren eigenen Werten.

## Feldgruppen

Hat ein Typ viele Felder, halten **Feldgruppen** das Formular lesbar. Eine Gruppe ist einfach ein benannter Abschnitt: Der vorgefertigte Comics-Typ gruppiert seine Felder unter "Verlagsdaten" und "Zustand und Bewertung". Lange Formulare lesen sich als übersichtliche Abschnitte statt als eine endlose Liste.

## Die vorgefertigten Typen

Ein frisches Konto kommt mit einem Dutzend vorgefertigter Typen, damit du nicht bei einer leeren Seite anfängst: Comics, Sammelkarten, Schallplatten, CD, DVD, Münzen, Briefmarken, Bücher, Actionfiguren / Spielzeug, Videospiele, Uhren und Wein, jeder mit sinnvoll gruppierten Feldern. Nutze sie so, wie sie sind, passe sie an oder ignoriere sie und baue deine eigenen.

:::note
Typen beschreiben Objekte, nicht Exemplare. Ein Feld, das sich pro physischem Stück unterscheidet, das du besitzt, wie Zustand oder eine Seriennummer, gehört stattdessen auf das Exemplar. Siehe @doc(items.itemsVsCopies).
:::

## Wie geht es weiter

- Baue oder passe einen Typ Schritt für Schritt an in @doc(collectionTypes.setup).
- Teile eine Typdefinition mit jemandem in @doc(collectionTypes.importExport).
- Sieh Felder in Aktion in @doc(items.addAndEdit).
