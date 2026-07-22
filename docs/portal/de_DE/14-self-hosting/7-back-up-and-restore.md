---
id: selfHosting.backupAndRestore
title: Deine Instanz sichern und wiederherstellen
slug: sichern-und-wiederherstellen
section: self-hosting-uebersicht
---

# Deine Instanz sichern und wiederherstellen

In KolleK gibt es kein automatisches Backup. Die Daten zu schützen ist Aufgabe des Betreibers, und diese Seite ist der Ablauf dafür. Sie ist heute auch die eigentliche Antwort auf "Wie exportiere ich alles", wie @doc(dataSafety.backupCollectionData) es aus Sicht des Sammlers erklärt.

## Was ein vollständiges Backup ist

Drei Dinge, und alle drei zählen:

1. **Die Datenbank**, im Volume `db-data`. Jeder Datensatz: Konten, Sammlungen, Objekte, Exemplare, Historie.
2. **Das Storage-Volume**, `storage-data`. Jedes hochgeladene Foto und Dokument.
3. **Der Anwendungsschlüssel**, `APP_KEY` aus deiner `.env` (plus `APP_PREVIOUS_KEYS`, falls gesetzt).

:::warning
Ein Backup ohne den passenden Anwendungsschlüssel ist kein Backup. Verschlüsselte Felder werden ohne den Schlüssel, mit dem sie geschrieben wurden, als unlesbarer Chiffretext wiederhergestellt. Bewahre den Schlüssel zusammen mit oder neben jedem Backup auf, das du erstellst. Siehe @doc(selfHosting.applicationKeyAndEncryption).
:::

## Sichern

Erstelle einen Datenbank-Dump:

```bash
docker compose exec mysql mysqldump -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" > kollek-backup.sql
```

Archiviere das Storage-Volume:

```bash
docker run --rm -v beaver_storage-data:/data -v "$PWD":/backup alpine tar czf /backup/kollek-storage.tar.gz -C /data .
```

Kopiere beide Dateien, sowie eine Kopie deiner `.env`, irgendwohin außerhalb des Servers. Automatisiere das mit einem nächtlichen Cronjob und behalte mehr als eine Generation. Ein Backup, aus dem du noch nie wiederhergestellt hast, ist eine Hoffnung, kein Plan.

## Wiederherstellen

Auf einer frischen Maschine stellst du in dieser Reihenfolge wieder her:

1. Installiere dieselbe KolleK-Version gemäß @doc(selfHosting.installDocker), setze aber `APP_KEY` (und `APP_PREVIOUS_KEYS`) aus deinem Backup, statt einen neuen Schlüssel zu generieren.
2. Starte den Stack einmal, damit die Volumes existieren, und lade dann den Datenbank-Dump:

```bash
docker compose exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" < kollek-backup.sql
```

3. Entpacke das Storage-Archiv in das Storage-Volume:

```bash
docker run --rm -v beaver_storage-data:/data -v "$PWD":/backup alpine tar xzf /backup/kollek-storage.tar.gz -C /data
```

4. Starte den Stack mit `docker compose up -d` neu und melde dich an, um alles zu überprüfen.

## Der Befehl, der alles löscht

:::warning
`docker compose down -v` entfernt die benannten Volumes, also die Datenbank und jede hochgeladene Datei. Verwende das Flag `-v` niemals auf einer echten Instanz. Einfaches `docker compose down` ist sicher und lässt die Volumes unangetastet.
:::

## Wie es weitergeht

- Verstehe, was der Schlüssel schützt, in @doc(selfHosting.applicationKeyAndEncryption).
- Sieh dir an, was Sammler von innerhalb der App exportieren können, in @doc(dataSafety.backupCollectionData).
