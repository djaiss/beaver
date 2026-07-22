---
id: collectionTypes.importExport
title: Einen Sammlungstyp importieren und exportieren
slug: einen-sammlungstyp-importieren-und-exportieren
section: organisieren
---

# Einen Sammlungstyp importieren und exportieren

Ein sorgfältig aufgebauter @doc(collectionTypes.overview, "Sammlungstyp") lohnt sich zu teilen. KolleK kann eine Typdefinition als JSON-Datei exportieren und wieder importieren, sodass du ein Setup zwischen Konten kopieren, mit einem anderen Sammler teilen oder eine Momentaufnahme vor einer Überarbeitung behalten kannst.

Du brauchst die Rolle Bearbeiter oder Eigentümer.

## Was mitgeht, und was nicht

Der Export enthält nur die Typdefinition: seinen Namen, seine Farbe, seine Feldgruppen, seine benutzerdefinierten Felder und die Optionen aller Auswahlfelder.

:::note
Beim Export eines Typs werden keine Objekte oder deren Daten exportiert. Aktuell gibt es keinen Import oder Export einzelner Objekte oder ganzer Sammlungen. Sieh dir die @doc(troubleshooting.featureStatus, "Seite zum Funktionsstatus") an, um zu sehen, wo das steht, und @doc(dataSafety.backupCollectionData) für die Portabilität, die es heute schon gibt.
:::

## Einen Typ exportieren

::::steps
:::step title="Den Typ öffnen"
Öffne in den Kontoeinstellungen **Sammlungstypen** und wähle den Typ, den du exportieren möchtest.
:::

:::step title="Exportieren"
Wähle **Exportieren**. KolleK lädt eine JSON-Datei mit der Beschreibung des Typs herunter.

::screenshot{label="Typ-Editor mit der Exportoption"}
:::
::::

Die Datei ist reiner Text. Du kannst sie lesen, bei deinen Backups aufbewahren oder jemandem schicken.

## Einen Typ importieren

Der Import funktioniert über eingefügtes JSON. Öffne also zuerst die erhaltene Datei in einem beliebigen Texteditor und kopiere ihren Inhalt.

::::steps
:::step title="Den Import starten"
Öffne in den Kontoeinstellungen **Sammlungstypen** und wähle **Importieren**.
:::

:::step title="Das JSON einfügen"
Füge die Typdefinition in das Feld ein und bestätige. KolleK prüft sie und erstellt den Typ mit seinen Gruppen, Feldern und Optionen.

::screenshot{label="Importformular mit eingefügtem JSON"}
:::

:::step title="Das Ergebnis prüfen"
Öffne den neuen Typ und prüfe, ob die Felder wie erwartet angekommen sind, dann hänge ihn an eine Sammlung an, um ihn zu nutzen.
:::
::::

## Ein durchgespieltes Beispiel

Noahs Freund sammelt ebenfalls Schallplatten und hat einen Typ "Schallplatten" mit gruppierten Feldern verfeinert: Veröffentlichungsinfo (Künstler, Album, Erscheinungsjahr) und Pressungsdetails (Pressung, Geschwindigkeit, farbiges Vinyl). Statt ihn von Hand nachzubauen, bittet Noah um den Export, fügt das JSON in sein eigenes Konto ein und hat die identische Struktur in Sekunden.

Wenn du das genaue Format sehen willst, das der Importer erwartet, exportiere zuerst einen bestehenden Typ, etwa den fertigen Comics-Typ, und nutze ihn als Vorlage. Deine eigenen Exporte lassen sich immer problemlos wieder importieren.

## Wie es weitergeht

- Verfeinere den importierten Typ in @doc(collectionTypes.setup).
- Erfahre, was sich sonst noch exportieren lässt und was nicht, in @doc(dataSafety.backupCollectionData).
