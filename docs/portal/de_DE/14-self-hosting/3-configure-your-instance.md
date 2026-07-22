---
id: selfHosting.configure
title: Deine Instanz konfigurieren
slug: instanz-konfigurieren
section: self-hosting-uebersicht
---

# Deine Instanz konfigurieren

Alles rund um deine Instanz wird über die `.env`-Datei konfiguriert, die du bei der @doc(selfHosting.installDocker, "Installation") erstellt hast. Diese Seite geht die Einstellungen durch, die ein Betreiber tatsächlich anfasst, gruppiert nach ihrer Funktion, statt jede einzelne Variable der Vorlage aufzulisten.

Nachdem du `.env` geändert hast, wendest du die Änderung an, indem du die Container neu erstellst:

```bash
docker compose up -d
```

## Identität und URL

- `APP_NAME` ist der Name, der in der Oberfläche und in E-Mails angezeigt wird. Standardmäßig `Kollek`.
- `APP_URL` ist die öffentliche Adresse deiner Instanz. Links in E-Mails werden daraus gebildet, sie muss also die Adresse sein, die deine Benutzer wirklich verwenden.
- `APP_PORT` ist der Host-Port, den der Web-Container veröffentlicht, standardmäßig `8000`.

## Der Anwendungsschlüssel

`APP_KEY` verschlüsselt sensible Daten im Ruhezustand. Du legst ihn einmal bei der Installation fest und änderst ihn nicht beiläufig. Er ist wichtig genug, um @doc(selfHosting.applicationKeyAndEncryption, "eine eigene Seite") zu bekommen, die auch den Rotationsmechanismus über `APP_PREVIOUS_KEYS` behandelt.

## Datenbank

`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` und `DB_ROOT_PASSWORD` konfigurieren den mitgelieferten MySQL-Container. Ändere beide Passwörter vor dem ersten Start von ihren Platzhaltern. `RUN_MIGRATIONS` steuert, ob der Web-Container beim Start migriert (standardmäßig `true`).

## E-Mail

`MAIL_MAILER` entscheidet, wie E-Mails deine Instanz verlassen, und ist standardmäßig `log`.

:::note
Mit dem Standardmailer `log` wird nie eine E-Mail tatsächlich versendet. Einladungen, Magic Links, Passwort-Zurücksetzungen und Sicherheitswarnungen werden stattdessen in das Anwendungsprotokoll geschrieben. Einen echten Mailer einzurichten ist der eine Schritt, den fast jede Instanz braucht. Siehe @doc(selfHosting.setupEmailDelivery).
:::

## Dateispeicher

`FILESYSTEM_DISK` ist standardmäßig `local`: hochgeladene Fotos und Dokumente werden im Volume `storage-data` gespeichert. Um stattdessen S3-kompatiblen Objektspeicher zu nutzen, setze es auf `s3` und trage die Variablen `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` und, bei Nicht-AWS-Anbietern, `AWS_ENDPOINT` ein. Dateien werden Benutzern in beiden Fällen über private, kontogeprüfte Routen ausgeliefert, nie als öffentliche URLs.

## Aufräumarbeiten

- `TRASH_RETENTION_DAYS` legt fest, wie lange weich gelöschte Objekte im @doc(dataSafety.restoreFromTrash, "Papierkorb") bleiben, bevor die nächtliche Bereinigung sie endgültig entfernt. Standard sind 30 Tage.
- `ACCOUNT_DELETION_NOTIFICATION_EMAIL` ist die Adresse, die benachrichtigt wird, wenn ein Benutzer seinen eigenen Benutzer löscht oder durch die @doc(users.inactiveDeletion, "Inaktivitätsbereinigung") entfernt wird. Trage hier dich selbst ein, damit Abgänge nicht unbemerkt bleiben.

## Die öffentliche Marketingseite

`SHOW_MARKETING_SITE` ist standardmäßig `false`, das heißt, deine Instanz liefert nur die Anwendung selbst aus. Setze es auf `true`, um zusätzlich die öffentlichen Marketingseiten und die generierte API-Referenz unter `/docs/api` auszuliefern. Die meisten privaten Instanzen lassen es ausgeschaltet, schalte es ein, wenn deine Entwickler die API-Referenz lokal bereitgestellt haben möchten.

## Was du nicht konfigurieren musst

Sessions (`SESSION_DRIVER`), Cache (`CACHE_STORE`) und die Warteschlange (`QUEUE_CONNECTION`) laufen von Haus aus alle über `database`. Die Standardwerte sind für den mitgelieferten Stack korrekt, und es gibt keinen Redis oder anderen Dienst hinzuzufügen. Lass sie unangetastet, es sei denn, du weißt genau, warum du sie änderst.

## Wie es weitergeht

- Bring echte E-Mails zum Laufen in @doc(selfHosting.setupEmailDelivery).
- Verstehe den Schlüssel, den du schützen musst, in @doc(selfHosting.applicationKeyAndEncryption).
- Richte @doc(selfHosting.backupAndRestore, "Backups") ein.
