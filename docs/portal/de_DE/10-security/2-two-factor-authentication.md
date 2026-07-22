---
id: security.twoFactorAuth
title: Schütze dein Konto mit Zwei-Faktor-Authentifizierung
slug: zwei-faktor-authentifizierung
section: sicherheit
---

# Schütze dein Konto mit Zwei-Faktor-Authentifizierung

Die Zwei-Faktor-Authentifizierung fügt der Anmeldung einen zweiten Schritt hinzu. Nachdem dein Passwort akzeptiert wurde, fragt KolleK nach einem sechsstelligen Code aus einer Authenticator-App auf deinem Handy. Selbst wenn jemand dein Passwort erfährt, kommt er ohne diesen Code nicht hinein.

Das ist die wirksamste Sicherheitskontrolle, die KolleK bietet, und sie ist in wenigen Minuten eingerichtet.

## Was du brauchst

Eine Authenticator-App auf deinem Handy, also jede App, die zeitbasierte Einmalcodes unterstützt. Hast du schon einmal einen QR-Code gescannt, um ein anderes Konto zu schützen, hast du bereits eine.

## Sie aktivieren

::::steps
:::step title="Öffne deine Sicherheitseinstellungen"
Gehe zu deinem Profil und öffne den Sicherheitsbereich, dann wähle, die **Zwei-Faktor-Authentifizierung** einzurichten.
:::

:::step title="Den QR-Code scannen"
KolleK zeigt einen QR-Code. Öffne deine Authenticator-App, füge ein neues Konto hinzu und scanne den Code. Die App zeigt jetzt einen sechsstelligen Code für KolleK, der sich alle 30 Sekunden ändert.

::screenshot{label="Einrichtungsbildschirm für die Zwei-Faktor-Authentifizierung mit dem QR-Code"}
:::

:::step title="Mit einem Code bestätigen"
Gib den aktuellen sechsstelligen Code aus deiner App in das Bestätigungsfeld ein und sende es ab. Das beweist, dass App und KolleK synchron sind, bevor sich etwas an deiner Anmeldung ändert.
:::

:::step title="Speichere deine Wiederherstellungscodes"
KolleK erzeugt acht Wiederherstellungscodes. Kopiere sie an einen sicheren Ort, der nicht dein Handy ist, etwa einen Passwort-Manager oder ein ausgedrucktes Blatt. Jeder Code kann dich einmal anmelden, falls du je deinen Authenticator verlierst.

::screenshot{label="Die acht Wiederherstellungscodes nach der Einrichtung"}
:::
::::

:::warning
Verlierst du deinen Authenticator und hast keine Wiederherstellungscodes, kannst du den Zwei-Faktor-Schritt nicht abschließen und riskierst, aus deinem Benutzer ausgesperrt zu werden. Speichere die Codes, bevor du die Seite schließt.
:::

## Was sich bei der Anmeldung ändert

Von jetzt an braucht die Anmeldung mit E-Mail und Passwort einen zusätzlichen Schritt. Nachdem dein Passwort akzeptiert wurde, fragt KolleK nach dem aktuellen Code aus deiner Authenticator-App. Gib ihn ein, und du bist drin.

Erreichst du deine App nicht, gib stattdessen einen deiner @doc(security.recoveryCodes, "Wiederherstellungscodes") ein.

:::note
Die Anmeldung über einen @doc(auth.magicLinks, "Magic Link") fragt nicht nach einem Zwei-Faktor-Code. Der Zugriff auf dein E-Mail-Postfach wirkt bereits als zweiter Faktor, schütze dieses Postfach also entsprechend.
:::

## Sie deaktivieren

Du kannst die Zwei-Faktor-Authentifizierung im selben Sicherheitsbereich deaktivieren. Das entfernt den Code-Schritt aus der Anmeldung und löscht auch deine Wiederherstellungscodes und die Kopplung mit deiner Authenticator-App. Aktivierst du sie später wieder, scannst du einen neuen QR-Code und erhältst einen frischen Satz Wiederherstellungscodes.

## Wie es weitergeht

- Stelle sicher, dass dein Notfallplan funktioniert: @doc(security.recoveryCodes).
- Verstehe den passwortlosen Weg und seinen Kompromiss: @doc(auth.magicLinks).
- Sieh dir jeden Weg in die App an: @doc(auth.signIn).
