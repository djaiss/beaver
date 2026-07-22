---
id: selfHosting.index
title: Self-Hosting-Übersicht
slug: self-hosting-uebersicht
section: self-hosting-uebersicht
---

# Self-Hosting-Übersicht

Eine eigene KolleK-Instanz zu betreiben ist ein vollwertiger, unterstützter Weg, das Produkt zu nutzen, und dazu kostenlos. Diese Seite erklärt dir, worauf du dich einlässt, bevor du irgendetwas installierst, und gibt dir die eine Regel mit, die wichtiger ist als alle anderen.

Wenn du dich noch nicht zwischen Self Hosting und einer gehosteten Instanz entschieden hast, beginne mit @doc(kollek.hostingOptions).

## Was der Betrieb einer Instanz bedeutet

KolleK wird als ein einziges Docker-Image ausgeliefert, das drei Rollen übernehmen kann, gesteuert über eine Umgebungsvariable:

- Die Rolle **web** stellt die Anwendung selbst bereit.
- Die Rolle **queue** arbeitet Hintergrundaufgaben ab (E-Mail-Versand, Webhook-Zustellungen, Protokollierung).
- Die Rolle **scheduler** führt die täglichen Wartungsaufgaben aus.

Die mitgelieferte Docker-Compose-Datei startet alle drei Rollen sowie eine MySQL-Datenbank. Sessions, Cache und die Warteschlange werden alle über die Datenbank abgewickelt, es gibt also keinen Redis oder sonstigen zusätzlichen Dienst zu betreiben. Hochgeladene Fotos und Dokumente liegen auf einem Storage-Volume, standardmäßig auf der lokalen Festplatte, wobei auch S3-kompatibler Speicher zur Verfügung steht.

Die Anforderungen sind bescheiden: eine Maschine mit Docker Engine 24 oder neuer und dem Compose-Plugin. Ein kleiner virtueller Server betreibt eine private Instanz problemlos.

## Die eine Regel, die du dir jetzt einprägen solltest

KolleK verschlüsselt sensible Daten im Ruhezustand mit dem Anwendungsschlüssel deiner Instanz.

:::warning
Lege den Anwendungsschlüssel einmal fest, bevor du die Instanz zum ersten Mal startest, und ändere ihn danach nie mehr auf einer laufenden Instanz. Wenn sich der Schlüssel ändert, werden jedes verschlüsselte Feld und jede Session dauerhaft unlesbar. Behandle den Schlüssel wie die Daten selbst: sichere ihn, und halte ihn auf allen Containern identisch.
:::

Das lohnt sich, gründlich zu lesen, bevor du installierst. @doc(selfHosting.applicationKeyAndEncryption) erklärt, was der Schlüssel schützt, wie du ihn aufbewahrst und den einen sicheren Weg, ihn bewusst zu rotieren.

## Deine Verantwortung

Self Hosting bedeutet, dass du der Betreiber bist. Konkret heißt das:

- **Installation und Updates.** Beides sind kurze, dokumentierte Docker-Abläufe.
- **Backups.** Es gibt kein automatisches Backup innerhalb der App. Du sicherst die Datenbank und das Storage-Volume selbst, zusammen mit dem Anwendungsschlüssel.
- **E-Mail-Zustellung.** Eine frische Instanz protokolliert E-Mails, statt sie zu versenden, sodass Einladungen und Anmeldelinks nirgendwohin gehen, bis du einen Mailer konfigurierst.
- **Die drei Rollen am Laufen halten.** Insbesondere Hintergrundaufgaben und die tägliche Wartung stoppen stillschweigend, wenn die Queue- oder Scheduler-Container nicht laufen.

Alex, der eine Instanz für seinen Sammlerclub betreibt, verbringt damit ein paar Minuten pro Monat, sobald die Ersteinrichtung erledigt ist. Es ist keine schwere operative Last, aber sie liegt bei dir.

## Dieser Abschnitt

Arbeite die Seiten ungefähr in dieser Reihenfolge durch:

1. @doc(selfHosting.installDocker). Von nichts zu einer laufenden Instanz.
2. @doc(selfHosting.configure). Die Umgebungsvariablen, die du tatsächlich anfassen wirst.
3. @doc(selfHosting.setupEmailDelivery). Sorge dafür, dass Einladungen und Magic Links wirklich versendet werden.
4. @doc(selfHosting.applicationKeyAndEncryption). Die wichtigste operative Regel.
5. @doc(selfHosting.upgrade). Sicher auf eine neue Version wechseln.
6. @doc(selfHosting.backupAndRestore). Die Daten schützen.
7. @doc(selfHosting.scheduledJobs). Was die App jede Nacht von selbst erledigt.
8. @doc(instanceAdmin.grantAccess). Den instanzweiten Administrator einrichten.
9. @doc(instanceAdmin.panel). Was dieser Administrator sehen und tun kann.
10. @doc(selfHosting.cliCommands). Die Artisan-Befehle, die ein Betreiber braucht.
11. @doc(selfHosting.addLanguage). Wie die Oberfläche übersetzt wird.

## Wie es weitergeht

- Bereit zu installieren? Gehe zu @doc(selfHosting.installDocker).
- Lieber eine geführte Ende-zu-Ende-Anleitung? Folge dem @doc(tutorials.selfHostWithDocker, "Self-Hosting-Tutorial").
