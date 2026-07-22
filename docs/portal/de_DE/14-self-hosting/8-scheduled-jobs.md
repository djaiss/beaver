---
id: selfHosting.scheduledJobs
title: Geplante Wartungsaufgaben
slug: geplante-wartungsaufgaben
section: self-hosting-uebersicht
---

# Geplante Wartungsaufgaben

Jede Nacht räumt deine Instanz hinter sich selbst auf. Diese Seite sagt dir, was läuft, wann, und was dafür erfüllt sein muss, damit dich nichts überrascht, was die App von selbst tut.

## Die nächtlichen Aufgaben

Drei Aufgaben laufen täglich, jede in der Warteschlange mit niedriger Priorität:

- **00:30 Uhr, Löschung inaktiver Benutzer.** Löscht Benutzer, die sich persönlich für die @doc(users.inactiveDeletion, "automatische Löschung nach Inaktivität") entschieden haben und seit sechs Monaten oder länger inaktiv sind. Jede Löschung wird an die in `ACCOUNT_DELETION_NOTIFICATION_EMAIL` hinterlegte Adresse gemeldet. Benutzer, die sich nie dafür entschieden haben, werden nie angerührt.
- **01:00 Uhr, Papierkorb-Bereinigung.** Löscht endgültig alles im @doc(dataSafety.restoreFromTrash, "Papierkorb"), das älter als die Aufbewahrungsfrist ist (`TRASH_RETENTION_DAYS`, standardmäßig 30 Tage). Innerhalb des Zeitfensters bleiben Objekte im Papierkorb wiederherstellbar.
- **02:00 Uhr, Kennzeichnung überfälliger Leihgaben.** Markiert aktive @doc(loans.lendAndBorrow, "Leihgaben"), deren Fälligkeitsdatum verstrichen ist, als überfällig, sodass Sammler auf einen Blick sehen, was nicht zurückgekommen ist.

Alle drei sind unbedenklich und erwartbar. Sie wirken nur auf Dinge, die Benutzer ausdrücklich gelöscht, aktiviert oder terminiert haben.

## Was laufen muss

Zwei Container sorgen dafür:

- Die Rolle **scheduler** entscheidet, dass es Zeit ist, und stellt jede Aufgabe in die Warteschlange.
- Die Rolle **queue** führt sie tatsächlich aus.

:::note
Wenn einer der beiden Container nicht läuft, stoppt die Wartung stillschweigend: Der Papierkorb sammelt sich über seine Aufbewahrungsfrist hinaus an, überfällige Leihgaben bleiben als aktiv markiert, und Benutzer, die der Inaktivitätsbereinigung zugestimmt haben, werden nicht bereinigt. Nichts geht kaputt, aber nichts läuft auch. Prüfe `docker compose ps`, wenn das nächtliche Verhalten gestoppt zu haben scheint.
:::

Alles holt beim nächsten erfolgreichen Lauf auf, eine verpasste Nacht ist kein Problem.

## Wie es weitergeht

- Passe das Aufbewahrungsfenster in @doc(selfHosting.configure) an.
- Sieh dir an, was Benutzer auf der anderen Seite erleben, in @doc(dataSafety.restoreFromTrash).
