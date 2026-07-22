---
id: security.index
title: Sicherheit im Überblick
slug: sicherheit
section: sicherheit
---

# Sicherheit im Überblick

KolleK speichert Daten, die dir wichtig sind: was du besitzt, was es wert ist und wo es sich befindet. Diese Seite gibt dir einen Überblick über die Kontrollen, die deinen Benutzer und deine Daten schützen, damit du entscheiden kannst, welche du aktivierst. Alle sind optional. Die meisten sind fünf Minuten deiner Zeit wert.

## Dein Passwort

Jedes Konto beginnt mit einem Passwort. KolleK setzt beim Festlegen zwei Regeln durch: Es muss mindestens acht Zeichen lang sein und wird gegen Listen von Passwörtern geprüft, die bei früheren Datenlecks bekannt geworden sind. Wird ein Passwort, das du versuchst, abgelehnt, liegt das daran, dass es in einer dieser Listen auftaucht. Wähle also etwas, das du nirgendwo sonst verwendet hast.

Du kannst dein Passwort jederzeit ändern und den Zugriff wiederherstellen, falls du es vergisst. Siehe @doc(auth.resetPassword).

## Zwei-Faktor-Authentifizierung

Das größte Upgrade, das du vornehmen kannst. Mit aktivierter Zwei-Faktor-Authentifizierung wird beim Anmelden mit deinem Passwort zusätzlich ein sechsstelliger Code aus einer Authenticator-App auf deinem Handy abgefragt. Ein gestohlenes Passwort allein reicht dann nicht mehr aus, um sich anzumelden.

Richte sie unter @doc(security.twoFactorAuth) ein, und stelle sicher, dass du @doc(security.recoveryCodes, "Wiederherstellungscodes") verstehst, bevor du dich darauf verlässt.

## Wiederherstellungscodes

Wenn du die Zwei-Faktor-Authentifizierung aktivierst, gibt dir KolleK acht Wiederherstellungscodes. Jeder davon kann einmal anstelle eines Authenticator-Codes verwendet werden, um wieder Zugriff zu bekommen, falls du dein Handy verlierst. Bewahre sie an einem sicheren Ort auf. @doc(security.recoveryCodes) erklärt, wie das geht.

## Magic Links

Ein passwortloser Weg, dich anzumelden. KolleK schickt dir per E-Mail einen Link, der dich direkt anmeldet und fünf Minuten lang gültig ist. Praktisch, mit einem Kompromiss, den du kennen solltest: Ein Magic Link fragt keinen Zwei-Faktor-Code ab, weil der Zugriff auf dein Postfach bereits als zweiter Faktor gilt. @doc(auth.magicLinks) erklärt, wann du sie nutzen solltest.

## API-Schlüssel

Wenn du die KolleK-API nutzt, authentifizierst du dich mit persönlichen API-Schlüsseln. Sie werden über dein Profil erstellt und widerrufen, und KolleK schickt dir jedes Mal eine E-Mail, wenn einer erstellt oder gelöscht wird, sodass ein Schlüssel, den du nicht selbst angelegt hast, nie unbemerkt bleibt. Siehe @doc(apiKeys.manage).

## Warn-E-Mails

KolleK achtet auf Ereignisse, über die es dich informieren sollte: ein fehlgeschlagener Anmeldeversuch, eine Anmeldung von einem neuen Gerät, eine Änderung deiner IP-Adresse, ein erstellter oder gelöschter API-Schlüssel. Passiert eines davon, bekommst du eine E-Mail. @doc(security.alertEmails) erklärt, was jede Warnung bedeutet und was du tun solltest.

## Eine sinnvolle Einrichtung

Wenn du nur zwei Dinge tust, dann diese:

1. Aktiviere die @doc(security.twoFactorAuth, "Zwei-Faktor-Authentifizierung").
2. Speichere deine @doc(security.recoveryCodes, "Wiederherstellungscodes") an einem Ort, der nicht dein Handy ist.

Alles andere in diesem Abschnitt kann warten, bis du es brauchst.

## Seiten in diesem Abschnitt

1. @doc(security.twoFactorAuth)
2. @doc(security.recoveryCodes)
3. @doc(auth.magicLinks)
4. @doc(auth.resetPassword)
5. @doc(security.alertEmails)
6. @doc(apiKeys.manage)
