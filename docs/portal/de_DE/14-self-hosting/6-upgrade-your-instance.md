---
id: selfHosting.upgrade
title: Deine Instanz aktualisieren
slug: instanz-aktualisieren
section: self-hosting-uebersicht
---

# Deine Instanz aktualisieren

Das Aktualisieren von KolleK ist bewusst unspektakulär gehalten: neuere Version ziehen, neu bauen, fertig. Diese Seite erklärt, warum das sicher ist, und den einen Schritt, den du nach dem Update kennen solltest.

## Warum bei Updates keine Daten verloren gehen

Zwei Eigenschaften machen den Update-Pfad sicher:

- **Deine Daten liegen in benannten Volumes** (`db-data` für die Datenbank, `storage-data` für Dateien), unabhängig von den Containern und dem Image. Container neu zu bauen rührt sie nicht an.
- **Migrationen wirken nur vorwärts.** Der Web-Container wendet ausstehende Datenbankmigrationen beim Start mit `migrate --force` an, und KolleK liefert nie eine Migration aus, die Daten zurücksetzt oder destruktiv umschreibt. Ein Update fügt dem Schema nur etwas hinzu.

## Aktualisieren

::::steps
:::step title="Erst sichern"
Erstelle einen Datenbank-Dump und ein Storage-Archiv wie in @doc(selfHosting.backupAndRestore) beschrieben. Updates sind von Natur aus sicher, aber ein Backup macht aus "von Natur aus sicher" ein "sicher, Punkt".
:::

:::step title="Die neue Version holen"
Ziehe im Repository-Verzeichnis die Version, auf die du aktualisieren möchtest:

```bash
git pull
```
:::

:::step title="Neu bauen und neu starten"
```bash
docker compose up -d --build
```

Compose baut das Image neu und erstellt die Container neu. Beim Start wendet der Web-Container automatisch alle neuen Migrationen an, danach ist die Instanz wieder unter deiner `APP_URL` erreichbar.
:::
::::

Wenn du Migrationen lieber manuell steuerst, setze `RUN_MIGRATIONS=false` und führe `docker compose exec app php artisan migrate --force` selbst als Teil des Ablaufs aus, wie in @doc(selfHosting.installDocker) beschrieben.

## Der Schritt für den Fotosuche-Index

Ein Update enthält eine einmalige Wartungsaufgabe: Instanzen, die älter sind als der Fotobibliotheks-Bildschirm, müssen ihren Fotosuche-Index einmalig aufbauen, sonst bleibt die Fotosuche für bestehende Fotos leer.

```bash
docker compose exec app php artisan photos:rebuild-search-index
```

Der Befehl ist idempotent und kann gefahrlos auf jeder Instanz ausgeführt werden. Im Zweifel also einfach ausführen. Er füllt außerdem nachträglich die Bildabmessungen für Fotos auf, die hochgeladen wurden, bevor Abmessungen erfasst wurden.

:::note
Ändere `APP_KEY` nicht im Rahmen eines Updates. Der Schlüssel überdauert jede Version. Falls eine Update-Anleitung jemals so klingt, als würde sie einen neuen Schlüssel verlangen, hast du sie missverstanden. Siehe @doc(selfHosting.applicationKeyAndEncryption).
:::

## Wie es weitergeht

- Halte @doc(selfHosting.backupAndRestore, "Backups") aktuell, damit jedes Update von einem sauberen Stand ausgeht.
- Sieh dir @doc(selfHosting.scheduledJobs) an, die automatisch weiterlaufen, sobald der Scheduler-Container wieder oben ist.
