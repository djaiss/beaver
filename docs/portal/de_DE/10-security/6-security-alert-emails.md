---
id: security.alertEmails
title: Anmelde- und Sicherheitswarnungen per E-Mail
slug: sicherheitswarnungen-per-e-mail
section: sicherheit
---

# Anmelde- und Sicherheitswarnungen per E-Mail

Von Zeit zu Zeit schickt dir KolleK vielleicht eine E-Mail, ohne dass du irgendetwas angefordert hast. Diese Warnungen stellen sicher, dass du, wenn rund um deinen Benutzer etwas passiert, davon zuerst von KolleK erfährst statt auf einem anderen Weg. Diese Seite listet jede Warnung auf, was sie bedeutet und was zu tun ist, wenn dich eine überrascht.

## Fehlgeschlagener Anmeldeversuch

**Wann sie eintrifft:** Jemand hat deine E-Mail-Adresse mit einem falschen Passwort auf der Anmeldeseite eingegeben.

**Warst du es selbst**, weil du dich vertippt hast, ignoriere sie.

**Warst du es nicht**, versucht jemand es mit deiner Adresse. Ein fehlgeschlagener Versuch ist meist nur Rauschen, aber wiederholte Warnungen bedeuten, dass deine E-Mail-Adresse gezielt angegriffen wird. Stelle sicher, dass dein Passwort einzigartig für KolleK ist, und aktiviere die @doc(security.twoFactorAuth, "Zwei-Faktor-Authentifizierung"), damit ein erratenes Passwort nicht reicht.

## Neue Anmeldung

**Wann sie eintrifft:** Eine erfolgreiche Anmeldung hat stattgefunden, und die E-Mail nennt das Gerät, von dem sie kam.

**Warst du es selbst**, an einem neuen Browser, Handy oder Computer, ignoriere sie.

**Warst du es nicht**, hat jemand dein Passwort. @doc(auth.resetPassword, "Ändere dein Passwort") sofort und überprüfe dein Konto auf alles Unerwartete.

## IP-Adressänderung

**Wann sie eintrifft:** Du hast dich von einer anderen Netzwerkadresse angemeldet als zuletzt.

Das ist normal, wenn du reist, das Netzwerk wechselst oder dein Anbieter Adressen rotiert. Es verdient nur Aufmerksamkeit, wenn es zusammen mit einer Anmeldung eintrifft, die du nicht wiedererkennst.

## API-Schlüssel erstellt, API-Schlüssel gelöscht

**Wann sie eintrifft:** Ein @doc(apiKeys.manage, "API-Schlüssel") wurde für deinen Benutzer erstellt oder widerrufen.

**Warst du es selbst**, beim Verwalten deiner Schlüssel, ignoriere sie.

**Warst du es nicht**, nimm es ernst. Ein unerwarteter Schlüssel bedeutet, dass jemand genug Zugriff hatte, um einen zu erstellen. Widerrufe den Schlüssel, ändere dein Passwort und prüfe deine verbleibenden Schlüssel und ihre letzten Nutzungszeiten.

:::note
Anmelde-Tokens, die beim Anmelden über die API erstellt werden, lösen die E-Mail für erstellte Schlüssel nicht aus. Nur Schlüssel, die du selbst per Hand erstellst, tun das, damit die Warnung aussagekräftig bleibt.
:::

## E-Mails, um die du gebeten hast

Zwei weitere E-Mails treffen nur ein, weil jemand sie angefordert hat, sie sind also für sich genommen keine Warnungen: die @doc(auth.magicLinks, "Magic-Link")-E-Mail und die E-Mail zum Zurücksetzen des Passworts. Erhältst du eine, die du nicht angefordert hast, hat jemand deine Adresse in dieses Formular eingegeben. Keine davon lässt sich ohne Zugriff auf dein Postfach nutzen, aber wiederholte, nicht angeforderte E-Mails sind ein weiteres Anzeichen dafür, dass sich jemand an deiner Adresse zu schaffen macht.

## Wenn wirklich etwas nicht stimmt

1. @doc(auth.resetPassword, "Ändere dein Passwort").
2. Aktiviere die @doc(security.twoFactorAuth, "Zwei-Faktor-Authentifizierung"), falls sie aus ist.
3. Überprüfe deine @doc(apiKeys.manage, "API-Schlüssel") und widerrufe alles, was du nicht wiedererkennst.
4. Prüfe @doc(activity.logAndSentEmails, "dein persönliches Aktivitätsprotokoll") auf Aktionen, die du nicht ausgeführt hast.

## Wie es weitergeht

- Sieh alles, was KolleK dir je geschickt hat, mit Zustellstatus: @doc(activity.logAndSentEmails, "Dein persönliches Aktivitätsprotokoll und gesendete E-Mails").
- Der vollständige Katalog jeder E-Mail, die KolleK versenden kann: @doc(reference.emailsSent).
