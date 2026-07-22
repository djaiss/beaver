---
id: api.authenticate
title: Mit der API authentifizieren
slug: mit-der-api-authentifizieren
section: entwickler
---

# Mit der API authentifizieren

Jede API-Anfrage wird mit einem Bearer-Token authentifiziert. Diese Seite führt dich von null zu deiner ersten erfolgreichen Anfrage und behandelt dann, wie du Tokens über die API selbst erhältst und widerrufst.

Ersetze `https://kollek.example.com` in den Beispielen durch die Adresse deiner Instanz. Die API liegt unter `/api` auf dieser Adresse.

## Der schnellste Weg: einen Schlüssel in der App erstellen

Der einfachste Weg zu einem Token ist, in deinem Profil einen API-Schlüssel zu erstellen.

::::steps
:::step title="Einen API-Schlüssel erstellen"
Öffne in der App deine Profileinstellungen und gehe zu **API-Schlüssel**. Erstelle einen Schlüssel und gib ihm ein Label, an dem du ihn später wiedererkennst, zum Beispiel "Reporting-Skript".

::screenshot{label="Profileinstellungen, Seite API-Schlüssel mit dem Formular für neue Schlüssel"}
:::

:::step title="Das Token kopieren"
Das Token wird direkt nach der Erstellung einmalig angezeigt. Kopiere es jetzt und bewahre es an einem sicheren Ort auf, zum Beispiel in einem Passwort-Manager. Falls du es verlierst, widerrufe den Schlüssel und erstelle einen neuen.
:::

:::step title="Deine erste Anfrage stellen"
Sende das Token im Header `Authorization`. Ein guter erster Aufruf ist `/api/me`, der deinen eigenen Benutzer zurückgibt:

```bash
curl https://kollek.example.com/api/me \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```
:::
::::

Wenn du ein JSON-Dokument mit den Daten deines Benutzers zurückbekommst, bist du authentifiziert. Das Erstellen und Widerrufen von Schlüsseln, und wann jeder zuletzt verwendet wurde, wird in @doc(apiKeys.manage) behandelt.

:::note
Tokens laufen nicht von selbst ab. Sie funktionieren, bis du sie widerrufst, behandle ein Token also wie ein Passwort.
:::

## Ein Token über die API erhalten

Du kannst dich auch vollständig über HTTP authentifizieren, was sich für Skripte und Integrationen eignet, die ihre eigenen Zugangsdaten verwalten.

Melde dich mit deiner E-Mail-Adresse und deinem Passwort an, um ein Token zu erhalten:

```bash
curl -X POST https://kollek.example.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "you@example.com",
    "password": "your-password",
    "device_name": "Reporting script"
  }'
```

Die Antwort enthält dein Token unter `data.token`. Das optionale `device_name` benennt das Token, damit du es später in deiner Schlüsselliste wiedererkennst.

Zwei Dinge solltest du wissen:

- Wenn für deinen Benutzer @doc(security.twoFactorAuth, "Zwei-Faktor-Authentifizierung") aktiviert ist, verlangt der Login-Endpunkt zusätzlich ein Feld `code` mit einem aktuellen TOTP-Code aus deiner Authenticator-App oder einem deiner @doc(security.recoveryCodes, "Wiederherstellungscodes").
- Auch die Registrierung über die API funktioniert: `POST /api/register` erstellt einen Benutzer mit einem eigenen Konto und liefert ein Token zurück, genau wie die Registrierung im Browser.

Beide Endpunkte sind auf 6 Anfragen pro Minute begrenzt, was für echte Anmeldungen mehr als genug ist und Brute-Force-Versuche stoppt.

## Tokens widerrufen

Du hast zwei Möglichkeiten:

- `DELETE /api/logout` widerruft das Token, mit dem die Anfrage gestellt wurde. Nutze das, wenn ein Skript mit einem temporären Token fertig ist.
- Die Seite **API-Schlüssel** in deinem Profil listet jedes Token auf und kann jedes davon widerrufen. Die API-Schlüssel-Endpunkte in der generierten Referenz tun dasselbe über HTTP.

KolleK schickt dir eine E-Mail, wenn ein Schlüssel in der App erstellt oder gelöscht wird, damit unerwartete Schlüsselaktivität nicht unbemerkt bleibt. Siehe @doc(security.alertEmails).

## Wie es weitergeht

- Lerne die Anfragekonventionen in @doc(api.rateLimitsAndConventions).
- Verwalte deine Tokens in @doc(apiKeys.manage).
- Erkunde jeden Endpunkt in der generierten Referenz unter `/docs/api`.
