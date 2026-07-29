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

Diese Seiten sind für jeden Besucher gleich und ändern sich nur beim Neuausrollen, deshalb wird jede mit Cache-Headern ausgeliefert: ein Browser behält sie fünf Minuten, jedes CDN vor der Instanz eine Woche. Setze `CACHE_PUBLIC_PAGES` auf `false`, um sie nicht mehr zu senden. Auf der Instanz selbst wird so oder so nichts zwischengespeichert.

Wenn du die Website über Cloudflare auslieferst, setze zusätzlich `CLOUDFLARE_API_TOKEN` und `CLOUDFLARE_ZONE_ID`. Sie sind es, die der Instanz erlauben, Cloudflare zum Verwerfen des Vorgehaltenen aufzufordern, wenn sich etwas ändert, das die öffentliche Website zeigt, von selbst oder über @doc(instanceAdmin.panel, "das Panel"). Lass sie leer auf einer direkt ausgelieferten Instanz: davor gibt es nichts zu leeren.

In beiden Fällen beantwortet die Instanz `/robots.txt` selbst, statt eine Datei von der Festplatte auszuliefern. Ist die öffentliche Website eingeschaltet, verweist sie Crawler auf `/sitemap.xml`, die jede öffentliche Seite in jeder angebotenen Sprache auflistet. Ist sie ausgeschaltet, weist sie jeden Crawler an, den gesamten Host zu meiden, denn auf einer privaten Instanz gibt es nichts, was indexiert werden sollte.

## Spam-Schutz

Nichts hindert einen Bot daran, dein Registrierungsformular auszufüllen, und auf einer Instanz, die jeder erreichen kann, passiert das früher oder später. `TURNSTILE_ENABLED` setzt ein Cloudflare-Turnstile-Widget auf die Formulare zum Anmelden, Registrieren und Zurücksetzen des Passworts, sodass eine Absendung, die es nicht gelöst hat, die Anwendung nie erreicht.

Standardmäßig ist es `false`, und das ist die richtige Antwort für eine private Instanz, auf der du jeden kennst, der sich anmeldet. Zum Einschalten legst du in deinem Cloudflare-Konto ein Turnstile-Widget an, setzt dann `TURNSTILE_ENABLED` auf `true`, `TURNSTILE_SITE_KEY` auf den öffentlichen Schlüssel, den du bekommst, und `TURNSTILE_SECRET_KEY` auf den privaten. Der Schalter allein schützt nichts: ohne beide Schlüssel kommt kein Besucher an der Prüfung vorbei.

:::note
Mit eingeschaltetem Widget prüft deine Instanz jede Absendung bei Cloudflare, sie muss also `challenges.cloudflare.com` erreichen können, damit sich überhaupt jemand anmelden kann. Eine Prüfung, die nicht stattfinden kann, gilt absichtlich als fehlgeschlagene Prüfung. Eine Instanz ohne verlässlichen ausgehenden Internetzugang sollte das ausgeschaltet lassen.
:::

## Was du nicht konfigurieren musst

Sessions (`SESSION_DRIVER`), Cache (`CACHE_STORE`) und die Warteschlange (`QUEUE_CONNECTION`) laufen von Haus aus alle über `database`. Die Standardwerte sind für den mitgelieferten Stack korrekt, und es gibt keinen Redis oder anderen Dienst hinzuzufügen. Lass sie unangetastet, es sei denn, du weißt genau, warum du sie änderst.

## Wie es weitergeht

- Bring echte E-Mails zum Laufen in @doc(selfHosting.setupEmailDelivery).
- Verstehe den Schlüssel, den du schützen musst, in @doc(selfHosting.applicationKeyAndEncryption).
- Richte @doc(selfHosting.backupAndRestore, "Backups") ein.
