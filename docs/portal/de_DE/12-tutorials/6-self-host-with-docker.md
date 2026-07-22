---
id: tutorials.selfHostWithDocker
title: "Tutorial: Hoste KolleK mit Docker selbst"
slug: kollek-mit-docker-selbst-hosten
section: tutorials
---

# Tutorial: Hoste KolleK mit Docker selbst

In diesem Tutorial führst du eine völlig leere Maschine zu einer laufenden KolleK-Instanz: Du klonst das Projekt, konfigurierst die Umgebung, generierst den Anwendungsschlüssel, startest den Stack, erstellst das erste Konto und vergibst den ersten Instanzadministrator. Am Ende hast du eine funktionierende Instanz und weißt, wo die tieferen operativen Anleitungen weitermachen.

Wir begleiten dabei Alex, der auf einem kleinen Heimserver eine Instanz für seinen Sammlerclub einrichtet. Die Schritte sind auf einem VPS oder einem Laptop identisch.

Rechne mit fünfzehn bis dreißig Minuten, das meiste davon Wartezeit für den ersten Build.

## Bevor du beginnst

Du brauchst:

- Eine Maschine mit **Docker Engine 24 oder neuer** und dem **Compose-Plugin** (der Befehl `docker compose`, nicht das ältere `docker-compose`).
- **Git**, um das Projekt zu klonen.
- Ein Terminal und ein wenig Erfahrung im Umgang mit Befehlen darin.

Es hilft auch, zuerst die @doc(selfHosting.index, "Self-Hosting-Übersicht") zu überfliegen, weil sie die eine Regel einführt, auf der dieses Tutorial besteht: Der Anwendungsschlüssel wird einmal festgelegt und danach nie mehr geändert.

## Schritt 1: Das Projekt klonen und deine Konfiguration erstellen

```bash
git clone https://github.com/djaiss/beaver.git
cd beaver
cp .env.docker.example .env
```

Die Datei `.env` ist die Konfiguration deiner Instanz. Alles, was ein Betreiber routinemäßig anfasst, liegt darin, und die @doc(selfHosting.configure, "Konfigurationsanleitung") geht sie Gruppe für Gruppe durch. Für den ersten Start sind nur die nächsten beiden Schritte notwendig.

## Schritt 2: Den Anwendungsschlüssel generieren

KolleK verschlüsselt sensible Daten im Ruhezustand mit einem Schlüssel, den du einmal generierst:

```bash
docker compose run --rm app php artisan key:generate --show
```

Kopiere die Ausgabe (sie beginnt mit `base64:`) und füge sie in `.env` als Wert von `APP_KEY` ein.

:::warning
Lege den Anwendungsschlüssel einmal fest und ändere ihn danach nie mehr auf einer laufenden Instanz. Alles Verschlüsselte, einschließlich Namen, Objekten und Sessions, wird unter einem anderen Schlüssel dauerhaft unlesbar. Bewahre eine Kopie des Schlüssels an einem sicheren Ort auf, denn ein Datenbank-Backup lässt sich nur mit dem Schlüssel wiederherstellen, mit dem es verschlüsselt wurde.
:::

Die vollständige Geschichte, einschließlich wie eine bewusste Schlüsselrotation unterstützt wird, steht in @doc(selfHosting.applicationKeyAndEncryption).

## Schritt 3: Passwörter und URL überprüfen

Öffne `.env` in einem Editor und prüfe drei Dinge:

- **`DB_PASSWORD` und `DB_ROOT_PASSWORD`.** Beide werden mit Platzhalterwerten ausgeliefert. Ändere sie vor dem ersten Start in eigene, starke Passwörter, denn beim ersten Start wird die Datenbank damit angelegt.
- **`APP_URL`.** Die Adresse, die deine Benutzer eingeben werden. Alex setzt `http://server.local:8000` für das Netzwerk des Vereins. Der Standardwert ist `http://localhost:8000`.
- **`APP_PORT`.** Der veröffentlichte Port, `8000`, sofern du ihn nicht änderst.

## Schritt 4: Den Stack starten

```bash
docker compose up -d --build
```

Der erste Lauf baut das Image und dauert ein paar Minuten. Compose startet dann vier Container:

- **app**, der Webserver. Das ist die einzige Rolle, die Datenbankmigrationen ausführt, das Schema wird also genau einmal eingerichtet.
- **queue**, der Hintergrund-Worker, der E-Mails versendet und Aufgaben verarbeitet.
- **scheduler**, der die täglichen Wartungsaufgaben ausführt.
- **mysql**, die Datenbank.

Prüfe mit `docker compose ps`, ob alles läuft. Wenn der app-Container als healthy gemeldet wird, öffne deine `APP_URL` in einem Browser. Du solltest den Anmeldebildschirm von KolleK sehen.

## Schritt 5: Das erste Konto erstellen

Gehe zur Registrierungsseite und melde dich an. Das funktioniert genau wie für jeden anderen Benutzer, die Anleitung dazu steht in @doc(accounts.create), und es macht dich zum Eigentümer des ersten Kontos der Instanz.

Alex registriert sich, landet auf der Checkliste für den Einstieg und widersteht der Versuchung, irgendetwas zu katalogisieren, bevor die Betreiberarbeit erledigt ist.

## Schritt 6: Den ersten Instanzadministrator einrichten

Ein Instanzadministrator kann über das Instanzadministrationspanel über alle Konten der Instanz hinweg sehen. Das Flag wird über die Kommandozeile vergeben:

```bash
docker compose exec app php artisan kollek:make-instance-administrator you@example.com
```

Verwende die E-Mail-Adresse, mit der du dich gerade registriert hast. Derselbe Befehl mit `--revoke` nimmt das Flag wieder zurück. Was das Flag tut, und bewusst nicht tut, behandelt @doc(instanceAdmin.grantAccess).

## Das Ergebnis

Du hast eine funktionierende Instanz: Die Web-App antwortet unter deiner URL, ein Queue-Worker und ein Scheduler laufen daneben, Daten liegen in einem benannten Datenbank-Volume, und du bist sowohl Kontoeigentümer als auch Instanzadministrator. Vereinsmitglieder können sich jetzt eigene Konten registrieren, oder du kannst @doc(tutorials.inviteHousehold, "Personen in deins einladen").

## Eine Sache, die du erledigen solltest, bevor du dich zurücklehnst

Von Haus aus schreibt die Instanz ausgehende E-Mails nur in eine Logdatei, statt sie zu versenden. Einladungen, Magic Links und Passwort-Zurücksetzungen gehen still und leise nirgendwohin, bis du einen echten Mailer konfigurierst. Das ist Absicht, und es zu beheben ist eine kurze Aufgabe: @doc(selfHosting.setupEmailDelivery).

## Häufige Fehler, die du vermeiden solltest

- **Den Anwendungsschlüssel verlieren.** Sichere ihn jetzt, getrennt von der Datenbank. Ohne ihn sind Backups nur Chiffretext.
- **Die Platzhalter-Datenbankpasswörter belassen.** Ändere sie vor dem ersten Start, nicht danach.
- **Die E-Mail-Einrichtung überspringen.** Die erste Meldung "meine Einladung ist nie angekommen" wird genau daran liegen.

## Wie es weitergeht

- Gehe jede Einstellung durch, die du übersprungen hast, in @doc(selfHosting.configure).
- Richte @doc(selfHosting.backupAndRestore, "Backups") ein, bevor der Katalog wertvoll wird.
- Wenn eine neue Version erscheint, folge @doc(selfHosting.upgrade).
