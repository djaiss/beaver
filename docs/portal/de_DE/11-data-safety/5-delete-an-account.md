---
id: accounts.delete
title: Ein Konto löschen
slug: ein-konto-loeschen
section: datensicherheit-und-pflege
---

# Ein Konto löschen

Das Löschen eines Kontos ist die zerstörerischste Aktion in KolleK. Sie entfernt den gesamten Arbeitsbereich: jede Sammlung, jedes Objekt, jedes Exemplar mit seinem vollständigen Verlauf, jedes Foto und Dokument, und den Zugriff jedes Mitglieds. Nur ein @doc(accounts.usersAndRoles, "Eigentümer") kann das tun.

:::warning
Das Löschen eines Kontos kann nicht rückgängig gemacht werden. Nichts wandert in den Papierkorb, nichts kann wiederhergestellt werden, und niemand, auch nicht die Person, die die Instanz betreibt, kann es zurückholen. Jedes Mitglied verliert alles auf einen Schlag.
:::

## Bevor du löschst

Halte inne und prüfe drei Dinge:

- **Willst du das wirklich, statt @doc(users.deleteSelf, "nur deinen eigenen Benutzer zu löschen")?** Um ein gemeinsames Konto zu verlassen, reicht es, dich selbst zu entfernen. Das Konto und der Katalog bleiben ohne dich bestehen.
- **Hängt jemand anderes davon ab?** Jedes Mitglied des Kontos verliert Zugriff und Daten in dem Moment, in dem du bestätigst. Sag es ihnen vorher.
- **Hast du daraus, was du brauchst?** Exportiere alle @doc(collectionTypes.importExport, "Sammlungstyp-Definitionen"), die du behalten willst. Ist die Instanz selbst gehostet, mach zuerst ein vollständiges Backup, wie beschrieben in @doc(selfHosting.backupAndRestore). Nach der Löschung gibt es nichts mehr zu sichern.

## Das Konto löschen

Finde in den **Kontoeinstellungen** die Löschoption im Gefahrenbereich und bestätige. Das Konto und alles darin werden entfernt, und alle Mitglieder werden endgültig abgemeldet.

## Was danach weg ist

Alles. Sammlungen, Objekte, Exemplare, Kategorien, Sets, Reihen, Tags, Standorte, benutzerdefinierte Typen und Felder, Fotos, Dokumente, die vollständigen Exemplarverläufe, der Aktivitätsverlauf, alle Mitglieder und alle ausstehenden Einladungen. Die beteiligten E-Mail-Adressen werden wieder frei für neue Konten, aber diese Konten starten leer.

## Wie es weitergeht

- Nur dich selbst zu entfernen wird behandelt in @doc(users.deleteSelf).
- Für wiederherstellbare Löschungen siehe @doc(dataSafety.restoreFromTrash).
