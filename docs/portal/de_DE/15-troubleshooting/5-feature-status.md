---
id: troubleshooting.featureStatus
title: Funktionsstatus und Roadmap
slug: funktionsstatus-und-roadmap
section: fehlerbehebung-und-faq
---

# Funktionsstatus und Roadmap

KolleK wächst, und ein paar Fähigkeiten sind sichtbar, bevor sie fertig sind. Diese Seite ist die einzige ehrliche Liste dessen, was heute vollständig verfügbar ist und was noch unterwegs ist, damit keine andere Seite drum herumreden muss. Bewegt sich das Produkt, bewegt sich diese Seite mit.

## Jetzt verfügbar

Alles andere, das in diesem Portal dokumentiert ist, funktioniert wie beschrieben, einschließlich:

- Sammlungen, Objekte, Exemplare, Fotos, Tags, Kategorien, Sets und Reihen.
- Sammlungstypen mit benutzerdefinierten Feldern, einschließlich Import und Export von Typdefinitionen als JSON.
- Der vollständige Exemplarverlauf: Transaktionen, Bewertungen, Versicherung, Leihgaben, Wartung, Herkunft, Standortgeschichte und Dokumente, mit dem vereinten Zeitverlauf.
- Zusammenarbeit mit den Rollen Eigentümer, Bearbeiter und Betrachter, sowie E-Mail-Einladungen.
- Zwei-Faktor-Authentifizierung, Magic Links, API-Schlüssel und Sicherheitswarn-E-Mails.
- Die vollständige JSON-API mit ihrer generierten Referenz unter `/docs/api`.
- Self Hosting mit Docker, verschlüsselte Daten bei der Speicherung, Papierkorb mit Wiederherstellung und Statistiken je Sammlung.

## Noch nicht

### Globale Suche

Die Suchleiste im Dashboard ist ein Platzhalter und durchsucht noch nichts. Was heute funktioniert: das Filtern der Objekte einer geöffneten Sammlung (siehe @doc(collections.chooseView)) und die Suche in der @doc(photos.library, "Fotobibliothek").

### Sammlungssichtbarkeit und Teilen

Jede Sammlung trägt eine Sichtbarkeitseinstellung (privat, geteilt oder öffentlich), und die Einstellung wird gespeichert, aber sie wird noch nicht durchgesetzt. Jedes Mitglied eines Kontos kann noch immer jede Sammlung darin durchsehen, und es gibt keinen öffentlichen Link, sodass eine als öffentlich markierte Sammlung von außerhalb des Kontos überhaupt nicht erreichbar ist. Lege die Sichtbarkeit jetzt fest, um deine Absicht festzuhalten; sie greift, sobald Teilen verfügbar wird. Siehe @doc(sharing.overview).

### Webhook-Zustellung

Du kannst Webhook-Endpunkte registrieren, und jeder erhält ein Signaturgeheimnis, aber noch kein Anwendungsereignis löst einen Webhook aus. Die Signatur- und Zustellmechanik steht bereit und wartet darauf, dass Ereignisse angeschlossen werden. Richte es jetzt gerne ein; Zustellungen kommen, sobald der Bereich wächst. Siehe @doc(webhooks.overview).

### Import und Export von Objekten und Sammlungen

Import und Export existieren nur für Sammlungstyp-Definitionen. Es gibt noch keinen Import oder Export auf Objekt- oder ganzer Sammlungsebene. Um alles herauszubekommen, haben Self-Hoster vollständige Instanz-Backups; siehe @doc(dataSafety.backupCollectionData).

### Instanzadministration: Support und Bewertungen

Im Instanzadministrationsbereich sind die Bereiche Support und Bewertungen Platzhalter, die das auch so sagen. Der Rest des Bereichs funktioniert; siehe @doc(instanceAdmin.panel).

## Wie du diese Seite liest

Nichts hier ist ein Versprechen mit Datum. "Noch nicht" bedeutet, dass die Grundlagen vielleicht existieren, aber du solltest nicht mit der Fähigkeit planen, bevor sie in die Liste oben wandert. Im Zweifel vertrau dieser Seite mehr als allem, was etwas anderes andeutet.

Fragen, die diese Seite nicht beantwortet, stehen wahrscheinlich in den @doc(troubleshooting.faq, "FAQ").
