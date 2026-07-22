---
id: dataSafety.backupCollectionData
title: Deine Sammlungsdaten sichern
slug: sammlungsdaten-sichern
section: datensicherheit-und-pflege
---

# Deine Sammlungsdaten sichern

"Wie bekomme ich meine Daten raus" verdient eine klare Antwort. Diese Seite sagt unumwunden, was KolleK heute aus der App heraus exportieren kann, was noch nicht, und was der eigentliche Backup-Weg in der Zwischenzeit ist.

## Was du heute exportieren kannst

**Sammlungstyp-Definitionen.** Ein @doc(collectionTypes.overview, "Sammlungstyp") kann als JSON-Datei exportiert werden (sein Name, seine Farbe, Feldgruppen, Felder und Optionen) und in jedes KolleK-Konto importiert werden. Siehe @doc(collectionTypes.importExport).

Das ist die ehrliche, vollständige Liste.

## Was du noch nicht exportieren kannst

Es gibt derzeit keinen eingebauten Export von Objekten, Exemplaren, Fotos oder ganzen Sammlungen, und keinen entsprechenden Import. Deine Katalogdaten lassen sich aus der Oberfläche heraus noch nicht als Datei aus der App ziehen.

:::note
Der Import und Export von Objekten und Sammlungen steht auf der Liste geplanter Fähigkeiten. Die @doc(troubleshooting.featureStatus, "Seite zum Funktionsstatus") ist die gepflegte Aufzeichnung, wo das steht, prüfe also dort nach, statt es anzunehmen.
:::

Brauchst du heute strukturierten Zugriff auf deine Daten, kann die @doc(api.overview, "JSON-API") alles in deinem Konto lesen, was für technisch versierte Personen ein gangbarer Weg ist.

## Der eigentliche Backup-Weg heute

Ist deine Instanz selbst gehostet, erfolgt das verlässliche Backup auf Instanzebene: ein Datenbank-Dump plus ein Archiv des Speicher-Volumes, das Fotos und Dokumente enthält. Das erfasst wirklich alles, einschließlich dessen, was der Export in der App nicht erreicht. Die Anleitung findest du in @doc(selfHosting.backupAndRestore).

Hostet jemand anderes KolleK für dich, liegt diese Backup-Fähigkeit bei dieser Person. Frag sie, wie ihre Backup-Vorkehrungen aussehen, das ist eine faire und wichtige Frage.

## Wie es weitergeht

- Selbst gehostet? Richte echte Backups ein in @doc(selfHosting.backupAndRestore).
- Eine Typ-Einrichtung zwischen Konten zu verschieben wird behandelt in @doc(collectionTypes.importExport).
- Sieh dir an, was sonst noch geplant ist, auf der @doc(troubleshooting.featureStatus, "Seite zum Funktionsstatus").
