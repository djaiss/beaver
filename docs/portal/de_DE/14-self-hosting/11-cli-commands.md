---
id: selfHosting.cliCommands
title: Mit der Kommandozeile verwalten
slug: mit-der-kommandozeile-verwalten
section: self-hosting-uebersicht
---

# Mit der Kommandozeile verwalten

Ein paar Betreiberaufgaben leben auf der Kommandozeile statt in der Web-App. Diese Seite listet die Artisan-Befehle auf, die du beim Betrieb einer Instanz tatsächlich brauchen könntest, mit einem Verweis auf die ausführlichere Seite zu jedem davon.

Bei einer Docker-Installation führst du jeden Befehl über den Web-Container aus:

```
docker compose exec app php artisan <command>
```

## Alltäglicher Betrieb

### Instanzadministration gewähren oder entziehen

```
php artisan beaver:make-instance-administrator you@example.com
php artisan beaver:make-instance-administrator you@example.com --revoke
```

Gewährt (oder entzieht) das serverweite Administrator-Flag für den Benutzer mit dieser E-Mail-Adresse. So wird der erste Administrator nach der Installation eingerichtet. Siehe @doc(instanceAdmin.grantAccess).

### Einen Webhook-Endpunkt erstellen

```
php artisan beaver:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

Registriert einen Webhook-Endpunkt für einen Benutzer und gibt dessen ID und Signaturschlüssel aus. Benutzer können das auch selbst aus ihren Profileinstellungen heraus tun. Beachte, dass momentan noch kein Anwendungsereignis Webhooks auslöst, siehe @doc(webhooks.overview).

### Den Fotosuche-Index neu aufbauen

```
php artisan photos:rebuild-search-index
```

Baut den Suchindex hinter der Fotobibliothek neu auf und füllt fehlende Bildabmessungen nach. Führe ihn einmal aus, nachdem du auf eine Version aktualisiert hast, die den Fotobildschirm einführt. Er kann jederzeit gefahrlos erneut ausgeführt werden, überspringt Fotos, deren Dateien fehlen, und ändert sonst nichts. Siehe @doc(selfHosting.upgrade).

### Ein Sprachgebiet für die Übersetzung anlegen

```
php artisan beaver:localize fr_FR
```

Extrahiert jeden übersetzbaren String der Anwendung und gleicht ihn mit der JSON-Datei des Sprachgebiets unter `lang/` ab. Siehe @doc(selfHosting.addLanguage).

## Nur für die Entwicklung

Es gibt zwei weitere Befehle im Code, und keiner davon gehört auf eine Produktivinstanz. `beaver:bruno` setzt die Datenbank mit Seed-Daten für das Testen von API-Clients zurück, was echte Daten zerstören würde, und `beaver:sync-skills` pflegt das eigene Tooling des Projekts. Als Betreiber kannst du beide ignorieren.

:::warning
Führe `beaver:bruno` niemals auf einer echten Instanz aus. Es löscht die Datenbank vollständig und füllt sie neu mit Demodaten.
:::

## Wie es weitergeht

- Richte deinen Administrator ein in @doc(instanceAdmin.grantAccess).
- Halte die Instanz aktuell mit @doc(selfHosting.upgrade).
- Übersetze die Oberfläche in @doc(selfHosting.addLanguage).
