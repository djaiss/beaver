---
id: tutorials.setupForHobby
title: "Tutorial: Richte dein Konto für ein bestimmtes Hobby ein"
slug: konto-fuer-hobby-einrichten
section: tutorials
---

# Tutorial: Richte dein Konto für ein bestimmtes Hobby ein

Ein Objekt hinzuzufügen ist einfach. Zweihundert hinzuzufügen ist nur einfach, wenn das Konto vorher vorbereitet wurde. In diesem Tutorial richtest du KolleK vor der Masseneingabe auf ein bestimmtes Hobby zu: Du formst den Sammlungstyp und seine benutzerdefinierten Felder, baust eine Standortkarte, die deinen echten Raum widerspiegelt, und legst ein Grundgerüst an Tags an, sodass jedes Objekt, das du danach hinzufügst, schnell und einheitlich erfasst wird.

Wir begleiten dabei Noah, der gerade dabei ist, rund dreihundert Schallplatten zu katalogisieren. Derselbe Ansatz funktioniert für jedes Hobby, setze also unterwegs einfach dein eigenes ein.

Rechne mit ungefähr einer halben Stunde, die dir später viele Stunden sparen wird.

## Bevor du beginnst

- Beende @doc(tutorials.catalogueFirstCollection, "Katalogisiere deine erste Sammlung von Anfang bis Ende") oder zumindest den @doc(gettingStarted.quickStart, "Schnellstart"), damit dir der Kernablauf vertraut ist.
- Kenne die Konzepte hinter @doc(collectionTypes.overview, "Sammlungstypen und benutzerdefinierten Feldern"), @doc(locations.overview, "Standorten") und @doc(tags.overview, "Tags"). Überflieg diese Seiten, falls nicht.
- Überlege dir, was du für jedes Objekt tatsächlich erfassen möchtest. Zehn Minuten mit einem Notizblock schlagen das nachträgliche Überarbeiten von Feldern nach fünfzig Einträgen.

## Schritt 1: Den Sammlungstyp formen

Noah startet mit dem fertigen Typ **Vinyl Records**, der mit seinem Konto kam. Er erfasst bereits My Rating, eine Gruppe **Release info** (Artist, Album, Release Year) und eine Gruppe **Pressing details** (Pressing/Edition, Speed, Color Vinyl).

Das kommt schon nah an das, was er will, aber er kauft viele japanische Pressungen und legt Wert auf den Zustand der Cover. Also passt er den Typ an.

::::steps
:::step title="Den Typ öffnen"
Gehe zu den Sammlungstyp-Einstellungen und wähle **Vinyl Records**. Der Editor speichert laufend, es gibt also keine Speichern-Schaltfläche zu suchen.

::screenshot{label="Sammlungstyp-Editor mit den Feldern von Vinyl Records"}
:::

:::step title="Die Felder hinzufügen, die du wirklich nutzt"
Noah fügt der Gruppe Pressing details ein Textfeld **Country of Pressing** hinzu und ein Feld **Sleeve Condition** als Auswahl mit den Optionen, nach denen er bewertet. Die verfügbaren Feldtypen sind Text, Zahl, Datum, Ja/Nein, Auswahl und Bewertung (bis zu fünf Sterne).
:::

:::step title="Felder gruppieren und ordnen"
Erstelle eine neue Gruppe, wenn eine Reihe von Feldern zusammengehört, und ziehe Felder in die Reihenfolge, in der sie auf dem Objektformular erscheinen sollen. Gruppen existieren einzig, um lange Formulare übersichtlich zu halten.
:::
::::

Warum das wichtig ist: Jetzt definierte benutzerdefinierte Felder erscheinen auf jedem Objektformular in jeder Sammlung, die diesen Typ verwendet. Sie im Voraus festzulegen bedeutet dreihundert einheitliche Datensätze statt dreihundert improvisierte.

:::note
Entwirf Felder für die Fragen, die du später stellen wirst. "Welche Platten sind farbiges Vinyl" lässt sich nur beantworten, wenn Color Vinyl ein Feld ist. Ein in einer Beschreibung vergrabenes Detail lässt sich nicht durchsuchen.
:::

## Schritt 2: Deine Standortkarte aufbauen

Noah bewahrt Platten an zwei Orten auf: einem Hörraum mit drei Regalen und Kisten im Lager. Er bildet genau das ab, denn ein Standort in KolleK ist nur nützlich, wenn er einem Ort entspricht, zu dem du physisch gehen kannst.

::::steps
:::step title="Die obersten Orte anlegen"
Erstelle in den @doc(locations.setup, "Standorteinstellungen") **Music Room** 🛋️ und **Storage** 📦. Das sind die Räume.
:::

:::step title="Die echten Unterteilungen verschachteln"
Erstelle unter Music Room **Shelf A**, **Shelf B** und **Shelf C**. Erstelle unter Storage **Crate 1** und **Crate 2**. Standorte verschachteln sich so tief wie nötig, eine Kiste in einer Box in einem Raum ist also kein Problem.
:::
::::

Warum das wichtig ist: Jedes Exemplar zeigt auf einen Standort, und spätere Umzüge werden als @doc(copies.move, "Standorthistorie") erfasst. Eine gute Karte sorgt jetzt dafür, dass "wo ist diese Platte" immer eine genaue Antwort hat.

## Schritt 3: Dein Tag-Vokabular anlegen

Tags durchqueren Sammlungen und Hierarchien, was sie ideal für Bezeichnungen macht, die sonst nirgendwo hinpassen. Noah erstellt seine Grundausstattung in den @doc(tags.manageAccount, "Tag-Einstellungen"): **Signed**, **First Pressing**, **Japanese Pressing**, **To Sell** und **Needs Cleaning**.

Zwei Gewohnheiten halten Tags nützlich:

- Halte sie wenige und wiederverwendbar. Ein Tag, der nur einmal verwendet wird, ist eine Tatsache, die eigentlich in ein Feld oder eine Notiz gehört hätte.
- Einige dich auf die Schreibweise, bevor andere dazustoßen. "Signed" und "Autographed" als getrennte Tags werden dich noch verfolgen.

Du kannst jederzeit spontan beim Bearbeiten eines Objekts einen Tag erstellen, diese Liste muss also nur die Bezeichnungen abdecken, die du bereits kennst.

## Schritt 4: Einen Typ importieren, statt ihn selbst zu bauen

Es gibt eine Abkürzung, die man kennen sollte. Ein Sammlungstyp kann als @doc(collectionTypes.importExport, "JSON exportiert und importiert werden"). Wenn ein Freund bereits einen guten Vinyl-Typ gebaut hat, kann er ihn exportieren, und du kannst ihn importieren, indem du das JSON einfügst, wodurch Name, Farbe, Gruppen, Felder und Auswahloptionen in einem Schritt übernommen werden.

:::note
Der Import eines Typs bringt nur die Typdefinition mit. Er importiert keine Objekte oder deren Daten. Es gibt derzeit keinen Import von Objekten oder ganzen Sammlungen, der ehrliche Stand dazu wird auf der @doc(troubleshooting.featureStatus, "Seite zum Funktionsstatus") festgehalten.
:::

Noah importiert einen Typ "45 RPM Singles", den ein Vereinsfreund geteilt hat, und er erscheint neben seinen eigenen Typen, bereit, einer Sammlung zugeordnet zu werden.

## Schritt 5: Die Sammlung erstellen und alles verbinden

Jetzt kommen die Teile zusammen.

::::steps
:::step title="Die Sammlung erstellen"
Noah erstellt eine Sammlung namens "Vinyl", wählt das Emoji 💿 und schreibt eine kurze Beschreibung.
:::

:::step title="Die benötigten Typen aktivieren"
Er aktiviert sowohl den Typ **Vinyl Records** als auch den importierten Typ **45 RPM Singles**. Eine Sammlung kann mehrere Typen nutzen, und jedes Objekt wählt den, der zu ihm passt.
:::

:::step title="Die Währung festlegen"
Er stellt die Sammlungswährung auf die ein, in der er tatsächlich Platten kauft. Sie kann vom Kontostandard abweichen, und alle Geldbeträge bei den Exemplaren dieser Sammlung werden darin angezeigt.
:::
::::

## Das Ergebnis

Füge jetzt eine Platte hinzu und spüre den Unterschied: Das Formular stellt genau die richtigen Fragen, das Standort-Dropdown bietet echte Regale an, und die Tags, die du brauchst, existieren bereits. Von hier an ist die Masseneingabe ein Rhythmus statt eine Reihe von Entscheidungen.

## Häufige Fehler, die du vermeiden solltest

- **Felder überkonstruieren.** Zehn Felder, die du ausfüllst, schlagen fünfundzwanzig, die du überspringst. Du kannst später Felder hinzufügen, sie nachträglich aufzufüllen ist der mühsame Teil.
- **Standorte, die nicht der Realität entsprechen.** Wenn es kein physisches Shelf B gibt, veraltet der Standort "Shelf B" sofort.
- **Tags für das nutzen, wofür Felder besser geeignet sind.** Eine Bewertung, ein Jahr oder eine Einstufung gehört in ein benutzerdefiniertes Feld, wo es ein echter Wert sein kann, keine Bezeichnung.

## Wie es weitergeht

- Beginne mit der Eingabe von Objekten in @doc(items.addAndEdit).
- Verfolge dein wertvollstes Stück gründlich in @doc(tutorials.trackValuableItem, "Verfolge das ganze Leben eines wertvollen Objekts").
- Arbeitest du mit anderen zusammen? @doc(tutorials.inviteHousehold, "Lade deinen Haushalt oder Verein ein").
