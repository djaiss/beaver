---
id: selfHosting.applicationKeyAndEncryption
title: Der Anwendungsschlüssel und Verschlüsselung
slug: anwendungsschluessel-und-verschluesselung
section: self-hosting-uebersicht
---

# Der Anwendungsschlüssel und Verschlüsselung

Diese Seite erklärt die wichtigste operative Regel beim Betrieb von KolleK. Alles andere an der Instanz lässt sich mit Geduld wiederherstellen. Dies ist die eine Einstellung, die Daten unwiderruflich zerstören kann.

## Was der Schlüssel bewirkt

KolleK verschlüsselt sensible Felder im Ruhezustand mit dem Anwendungsschlüssel der Instanz, dem Wert `APP_KEY` in deiner `.env`. Namen, Objektdetails, Werte benutzerdefinierter Felder, Dateinamen, E-Mail-Datensätze, Webhook-Secrets: rund dreißig Modelle enthalten verschlüsselte Spalten. Was für diese Felder in der Datenbank landet, ist Chiffretext, unlesbar ohne den Schlüssel. Derselbe Schlüssel schützt auch die Benutzersessions.

Das ist es, was @doc(dataSafety.howProtected) aus Sicht der Benutzer beschreibt. Operativ bedeutet das: Der Schlüssel ist kein Konfigurationsdetail. Er ist die halbe Wahrheit deiner Daten.

## Die Regel

:::warning
Lege den Anwendungsschlüssel einmal fest, bevor du die Instanz zum ersten Mal startest, und ändere ihn danach nie mehr auf einer laufenden Instanz. Geht der Schlüssel verloren oder wird er geändert, werden jede verschlüsselte Spalte und jede Session dauerhaft unlesbar. Es gibt keine Wiederherstellung, keinen Support-Weg und kein Werkzeug, das die Daten zurückbringen kann.
:::

Drei praktische Konsequenzen:

- **Sichere den Schlüssel zusammen mit den Daten.** Ein Datenbank-Backup ohne den passenden Schlüssel stellt nur Chiffretext wieder her. Bewahre den Schlüssel in einem Passwortmanager oder Secrets-Store auf, getrennt vom Server.
- **Halte ihn überall identisch.** Alle drei Anwendungscontainer (web, queue, scheduler) müssen mit demselben Schlüssel laufen. Die mitgelieferte Compose-Datei teilt sich eine `.env`, was das automatisch sicherstellt. Bewahre diese Eigenschaft in jedem angepassten Deployment.
- **Generiere ihn nicht "zur Sicherheit" neu.** `key:generate` gegen eine laufende Instanz auszuführen ist das klassische selbstverschuldete Desaster. Die Instanz weigert sich genau deshalb, ohne Schlüssel zu starten, damit niemand versehentlich eine schlüssellose Instanz startet und mitten im Betrieb einen neuen Schlüssel generiert.

## Den Schlüssel bewusst rotieren

Manche Betreiber müssen Schlüssel aus Compliance-Gründen nach einem Zeitplan rotieren. KolleK unterstützt das über frühere Schlüssel: Der aktuelle `APP_KEY` verschlüsselt alles Neue, während die in `APP_PREVIOUS_KEYS` (durch Komma getrennt) aufgeführten Schlüssel weiterhin bestehende Daten entschlüsseln können.

```bash
APP_KEY=base64:NEW_KEY_HERE
APP_PREVIOUS_KEYS=base64:OLD_KEY_HERE
```

Generiere einen neuen Schlüssel mit `php artisan key:generate --show` (niemals das einfache `key:generate`, das deinen aktiven Schlüssel überschreibt), verschiebe den alten Schlüssel nach `APP_PREVIOUS_KEYS`, setze den neuen als `APP_KEY` und erstelle die Container neu.

:::warning
Entferne nie einen Schlüssel aus `APP_PREVIOUS_KEYS`, solange noch Daten existieren, die damit verschlüsselt wurden. Daten werden nur dann mit dem neuen Schlüssel neu verschlüsselt, wenn sie erneut geschrieben werden, alte Datensätze können also auf unbestimmte Zeit vom alten Schlüssel abhängen.
:::

Wenn du nicht zur Rotation verpflichtet bist, ist die einfachste sichere Regel: ein Schlüssel, einmal festgelegt, gut gesichert.

## Wie es weitergeht

- Stelle sicher, dass der Schlüssel Teil deines @doc(selfHosting.backupAndRestore, "Backup- und Wiederherstellungsplans") ist.
- Lies die benutzerseitige Sicht auf Verschlüsselung in @doc(dataSafety.howProtected).
