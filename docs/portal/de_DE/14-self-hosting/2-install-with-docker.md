---
id: selfHosting.installDocker
title: Installation mit Docker
slug: installation-mit-docker
section: self-hosting-uebersicht
---

# Installation mit Docker

Dies ist die maßgebliche Installationsanleitung. Sie führt dich von einer Maschine mit Docker zu einer laufenden KolleK-Instanz mit deinem ersten erstellten Konto. Rechne mit ungefähr fünfzehn Minuten für das Ganze.

Die Datei `docker/README.md` im Repository dokumentiert denselben Ablauf aus Sicht des Betreibers und wird synchron zum Code gehalten. Falls diese Seite und diese Datei sich jemals widersprechen, vertraue `docker/README.md`.

## Bevor du beginnst

Du brauchst:

- Eine Maschine mit **Docker Engine 24 oder neuer** und dem **Compose-Plugin** (`docker compose`).
- Eine Kopie des KolleK-Repositorys, geklont oder heruntergeladen.
- Zehn Minuten Aufmerksamkeit für die Umgebungsdatei. Dort passieren die Fehler, die wirklich zählen.

Mehr nicht. Der Stack bringt seine eigene MySQL-Datenbank mit, und Sessions, Cache und die Warteschlange werden über die Datenbank abgewickelt, es gibt also keinen Redis zu installieren.

## Installation

::::steps
:::step title="Umgebungsdatei erstellen"
Kopiere im Repository-Root die Docker-Umgebungsvorlage:

```bash
cp .env.docker.example .env
```

Diese Datei steuert den gesamten Stack. Du bearbeitest sie in den nächsten beiden Schritten.
:::

:::step title="Anwendungsschlüssel generieren"
Generiere einen Schlüssel und kopiere die Ausgabe:

```bash
docker compose run --rm app php artisan key:generate --show
```

Füge den ausgegebenen Wert in `.env` als `APP_KEY` ein. Dieser Schlüssel verschlüsselt deine Daten im Ruhezustand. **Lege ihn jetzt fest und ändere ihn später nie mehr.** Ein geänderter Schlüssel macht jedes verschlüsselte Feld und jede Session dauerhaft unlesbar. Lies @doc(selfHosting.applicationKeyAndEncryption), bevor du weitermachst, falls du das noch nicht getan hast.
:::

:::step title="Passwörter und URL überprüfen"
Ändere in `.env` `DB_PASSWORD` und `DB_ROOT_PASSWORD` von ihren Platzhalterwerten und setze `APP_URL` auf die Adresse, die deine Benutzer aufrufen werden. Der Standardwert ist `http://localhost:8000`, was für einen ersten Versuch auf deiner eigenen Maschine passt.
:::

:::step title="Den Stack starten"
Baue und starte alles:

```bash
docker compose up -d --build
```

Der erste Build dauert ein paar Minuten. Wenn er fertig ist, wendet der Web-Container automatisch die Datenbankmigrationen an, und die Instanz ist unter deiner `APP_URL` erreichbar.
:::

:::step title="Dein erstes Konto erstellen"
Öffne die URL in einem Browser und nutze die Registrierungsseite, um dich anzumelden. Dadurch werden dein persönlicher Benutzer und dein erstes Konto angelegt, genau wie in @doc(accounts.create) beschrieben.

::screenshot{label="Registrierungsseite einer frisch installierten Instanz"}
:::

:::step title="Dir selbst Instanzadministrator-Zugriff gewähren"
Wenn du das instanzweite Administrationspanel nutzen möchtest, gewähre deinem Benutzer das Flag:

```bash
docker compose exec app php artisan kollek:make-instance-administrator you@example.com
```

Was das gibt und was nicht, erfährst du unter @doc(instanceAdmin.grantAccess).
:::
::::

## Was tatsächlich läuft

Der Compose-Stack startet vier Container. Drei davon führen dasselbe KolleK-Image in unterschiedlichen Rollen aus, gesteuert über die Umgebungsvariable `CONTAINER_ROLE`:

- **app** stellt die Webanwendung über nginx und PHP bereit. Es ist der einzige Container, der Datenbankmigrationen ausführt, und das tut er beim Start.
- **queue** verarbeitet Hintergrundaufgaben (E-Mail, Zustellungen, Protokollierung) aus den Warteschlangen `high`, `default` und `low`.
- **scheduler** löst die täglichen Wartungsaufgaben aus, die in @doc(selfHosting.scheduledJobs) beschrieben sind.

Der vierte Container ist **mysql** mit MySQL 8.4.

Deine Daten liegen in zwei benannten Docker-Volumes, unabhängig von den Containern: `db-data` für die Datenbank und `storage-data` für hochgeladene Fotos und Dokumente. Container können jederzeit neu gebaut und ersetzt werden, die Volumes bleiben bestehen.

:::note
Alle drei Anwendungscontainer müssen dieselbe `.env` verwenden, vor allem denselben `APP_KEY`. Die Compose-Datei richtet das bereits so ein. Behalte das bei, wenn du das Setup anpasst.
:::

## Wenn du Migrationen lieber selbst ausführen möchtest

Standardmäßig migriert der Web-Container die Datenbank bei jedem Start, was Updates weitgehend automatisch macht. Wenn du manuelle Kontrolle möchtest, setze `RUN_MIGRATIONS=false` in `.env` und führe Migrationen dann bei Bedarf selbst aus:

```bash
docker compose exec app php artisan migrate --force
```

## Wie es weitergeht

- Gehe @doc(selfHosting.configure) durch, um zu verstehen, was `.env` sonst noch steuert.
- Bring E-Mails zum Laufen in @doc(selfHosting.setupEmailDelivery). Bis dahin landen Einladungen und Anmeldelinks in einer Log-Datei statt in einem Postfach.
- Richte @doc(selfHosting.backupAndRestore, "Backups") ein, bevor du echte Daten einspielst.
