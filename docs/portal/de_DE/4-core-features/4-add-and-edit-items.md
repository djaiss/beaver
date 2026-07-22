---
id: items.addAndEdit
title: Objekte hinzufügen und bearbeiten
slug: objekte-hinzufuegen-und-bearbeiten
section: kernfunktionen
---

# Objekte hinzufügen und bearbeiten

Das ist die Seite für das, was du am häufigsten tun wirst: Einträge in deinen Katalog aufnehmen. Sie führt Feld für Feld durch das Objektformular, erklärt, welche Teile optional sind (fast alle), und behandelt das Bearbeiten und Löschen.

Ist dir der Unterschied zwischen einem Objekt und einem Exemplar noch nicht klar, lies zuerst @doc(items.itemsVsCopies). Kurz gesagt: Das Objekt beschreibt die Art des Dings, die Exemplare erfassen, was du physisch besitzt.

## Wer das darf

Objekte hinzuzufügen und zu bearbeiten erfordert die @doc(accounts.usersAndRoles, "Rolle") **Bearbeiter** oder **Eigentümer**.

## Ein Objekt hinzufügen

::::steps
:::step title="Die Sammlung öffnen"
Öffne die Sammlung, zu der das Objekt gehört, und wähle **Neues Objekt**.

::screenshot{label="Sammlungsansicht, Schaltfläche Neues Objekt"}
:::

:::step title="Benennen"
Gib den **Namen** ein. Das ist das einzige Pflichtfeld. Emma tippt "Amazing Spider-Man #300". Alles andere kann jetzt oder später ergänzt werden.
:::

:::step title="Einordnen"
Wähle optional einen **Typ**, eine **Kategorie**, ein **Set** und eine **Reihe**, und füge **Tags** hinzu. Der Typ ist der wichtige: Ihn zu wählen lässt die benutzerdefinierten Felder dieses Typs im Formular erscheinen.

::screenshot{label="Objektformular, Felder für Typ und Einordnung"}
:::

:::step title="Die Details ausfüllen"
Fülle die **benutzerdefinierten Felder**, die der Typ bereitstellt, lade **Fotos** hoch und erfasse die **Exemplare**, die du besitzt, alles im selben Formular.
:::

:::step title="Speichern"
Speichere das Objekt. Es erscheint sofort in der Sammlung.
:::
::::

## Das Formular, Feld für Feld

- **Name.** Erforderlich, und das Einzige, was es ist.
- **Beschreibung.** Freitext für alles, was sonst nirgends passt.
- **Typ.** Welcher @doc(collectionTypes.overview, "Sammlungstyp") dieses Objekt hat. Nur in der Sammlung aktivierte Typen werden angeboten. Der Typ legt fest, welche benutzerdefinierten Felder darunter erscheinen.
- **Kategorie.** Wo das Objekt innerhalb dieser Sammlung abgelegt wird. Siehe @doc(categories.organizeItems).
- **Set.** Eine endliche Liste, die du vervollständigst. Siehe @doc(sets.trackCompletion).
- **Reihe.** Ein Franchise, das sich über Sammlungen erstrecken kann. Siehe @doc(series.groupFranchise).
- **Tags.** Wähle bestehende @doc(tags.overview, "Tags") oder tippe ein neues ein, es wird sofort erstellt.
- **Benutzerdefinierte Felder.** Was auch immer der gewählte Typ definiert: Text, Zahlen, Daten, Ja-oder-Nein-Schalter, Auswahllisten und Bewertungen bis zu fünf Sternen. Felder erscheinen gruppiert, so wie der Typ sie organisiert.
- **Fotos.** Vollständig behandelt in @doc(items.addPhotos).
- **Exemplare.** Ein oder mehrere physische Exemplare, direkt eingefügt. Vollständig behandelt in @doc(copies.track).

Fühl dich nicht verpflichtet, alles auf einmal auszufüllen. Ein Name jetzt und Details später ist ein völlig guter Arbeitsablauf, und dasselbe Formular deckt beides ab.

## Ein Objekt bearbeiten

Öffne das Objekt und wähle, es zu bearbeiten. Es ist dasselbe Formular, bereits ausgefüllt. Ändere, was nötig ist, und speichere.

## Ein Objekt löschen

Öffne das Objekt, wähle, es zu löschen, und bestätige.

:::warning
Löschst du ein Objekt, wandert es mitsamt seinen Exemplaren in den Papierkorb. Es wird nach der Aufbewahrungsfrist (standardmäßig 30 Tage) endgültig entfernt.
:::

Bis dahin kannst du es zurückholen. Siehe @doc(dataSafety.restoreFromTrash).

## Wie es weitergeht

- Erfasse, was du physisch besitzt: @doc(copies.track).
- Mach den Katalog visuell: @doc(items.addPhotos).
- Beginne, Geld und Verlauf zu erfassen: @doc(copyHistory.concept, "Der Verlauf eines Exemplars erklärt").
