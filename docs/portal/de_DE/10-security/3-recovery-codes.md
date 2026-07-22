---
id: security.recoveryCodes
title: Speichere und nutze deine Wiederherstellungscodes
slug: wiederherstellungscodes
section: sicherheit
---

# Speichere und nutze deine Wiederherstellungscodes

Wiederherstellungscodes sind dein Weg zurück hinein, falls du deinen Authenticator verlierst. Aktivierst du die @doc(security.twoFactorAuth, "Zwei-Faktor-Authentifizierung"), erzeugt KolleK acht davon. Jeder Code funktioniert genau einmal, anstelle eines Codes aus deiner App.

Handys gehen verloren, gehen kaputt oder werden ersetzt. Wiederherstellungscodes sind das, was zwischen diesem gewöhnlichen Pechtag und dem Aussperren aus deinem Katalog steht.

## Woher du sie bekommst

Die Codes werden direkt gezeigt, nachdem du die Zwei-Faktor-Einrichtung bestätigt hast. Genau dann solltest du sie speichern.

Gute Orte, um sie aufzubewahren:

- Ein Passwort-Manager, in den Notizen deines KolleK-Eintrags.
- Ein ausgedrucktes Blatt in einer Schublade zu Hause.
- Eine verschlüsselte Datei, die du sicherst.

Ein schlechter Ort, um sie aufzubewahren, ist allein dein Handy, denn genau in der Situation, in der du sie brauchst, ist dein Handy weg.

:::warning
Verlierst du sowohl deinen Authenticator als auch deine Wiederherstellungscodes, kannst du den Zwei-Faktor-Schritt nicht abschließen und riskierst, aus deinem Benutzer ausgesperrt zu werden. Es gibt keinen Selbstbedienungsweg drumherum, speichere die Codes also jetzt an einem sicheren Ort.
:::

## Einen Code zur Anmeldung nutzen

Fragt KolleK nach deinem sechsstelligen Authenticator-Code und du kannst keinen liefern:

1. Gib bei der Zwei-Faktor-Abfrage stattdessen einen deiner Wiederherstellungscodes ein.
2. Du wirst wie gewohnt angemeldet.

Mehr steckt nicht dahinter. Die Abfrage akzeptiert entweder einen aktuellen Authenticator-Code oder einen unbenutzten Wiederherstellungscode.

## Jeder Code funktioniert einmal

Ein Wiederherstellungscode wird in dem Moment verbraucht, in dem du ihn nutzt. Er funktioniert danach nie wieder, deine übrigen Codes bleiben gültig. Streiche benutzte Codes durch, wo auch immer du sie aufbewahrst.

:::note
Gehen dir die Codes aus, oder vermutest du, dass jemand anderes sie gesehen hat, deaktiviere die Zwei-Faktor-Authentifizierung und aktiviere sie wieder. Das erneute Aktivieren erzeugt einen frischen Satz von acht Codes und macht die alten ungültig.
:::

## Nachdem du wieder drin bist

Hast du einen Wiederherstellungscode genutzt, weil du deinen Authenticator endgültig verloren hast, nimm dir zwei Minuten, um alles richtig einzurichten: Deaktiviere die Zwei-Faktor-Authentifizierung in deinen Sicherheitseinstellungen und aktiviere sie dann mit deinem neuen Gerät erneut. Du bekommst einen neuen QR-Code zum Scannen und einen frischen Satz Wiederherstellungscodes zum Speichern.

## Wie es weitergeht

- Richte den Code-Schritt selbst ein oder setze ihn zurück: @doc(security.twoFactorAuth).
- Auf andere Weise ausgesperrt? Siehe @doc(troubleshooting.signIn).
