---
id: instanceAdmin.grantAccess
title: Instanzadministrator-Zugriff gewähren
slug: instanzadministrator-zugriff-gewaehren
section: self-hosting-uebersicht
---

# Instanzadministrator-Zugriff gewähren

Ein Instanzadministrator ist die Person, die sich um den Server selbst kümmert, mit einem Panel, das über alle Konten der Instanz hinweg Einblick hat. Diese Seite erklärt, was das Flag ist, wie du es vergibst, und welche Schutzmechanismen es gibt.

## Was das Flag ist, und was nicht

Das Instanzadministrator-Flag gilt serverweit und ist vollständig getrennt von @doc(accounts.usersAndRoles, "Kontorollen"). Es gewährt genau eine Sache: Zugriff auf das @doc(instanceAdmin.panel, "Instanzadministrationspanel").

- Es gibt keine zusätzlichen Befugnisse innerhalb des eigenen Kontos des Administrators. Ein Instanzadministrator, der in seinem Konto Betrachter ist, kann dort weiterhin keine Objekte bearbeiten.
- Es gilt pro Benutzer, nicht pro Konto. Vergib es an die konkrete Person, die den Server betreibt, typischerweise dich selbst.

Alex, der die Instanz des Vereins betreibt, trägt das Flag auf seinem eigenen Benutzer und ist innerhalb seines eigenen Kontos ein ganz normaler Eigentümer. Die beiden Tatsachen haben nichts miteinander zu tun.

## Vergeben und entziehen

Das Flag wird über die Kommandozeile verwaltet, und das ist Absicht: Der erste Zugriff auf das serverweite Panel sollte Zugriff auf den Server voraussetzen.

```bash
docker compose exec app php artisan kollek:make-instance-administrator you@example.com
```

Entziehe es auf demselben Weg:

```bash
docker compose exec app php artisan kollek:make-instance-administrator you@example.com --revoke
```

Ein bestehender Administrator kann das Flag bei anderen Benutzern auch direkt aus dem Panel heraus umschalten.

## Warum das Panel so tut, als würde es nicht existieren

Für jeden ohne das Flag antwortet `/instance-admin` mit **404 Not Found**, nicht mit "Zugriff verweigert". Das Panel kündigt seine Existenz gegenüber Personen, die es nicht nutzen können, nicht an, sodass das Abtasten einer Instanz nichts preisgibt. Wenn du dir selbst das Flag vergeben hast und trotzdem eine 404 siehst, prüfe, ob du genau als der Benutzer angemeldet bist, dem du es vergeben hast.

## Die Schutzmechanismen gegen Aussperrung

Zwei Regeln schützen die Instanz davor, ihren Administrator zu verlieren:

- Ein Administrator kann sich sein eigenes Flag nicht selbst über das Panel entziehen.
- Ein Administrator kann seinen eigenen Benutzer nicht über das Panel löschen.

Das Panel kann also nie dazu benutzt werden, alle vom Panel auszusperren. Und selbst wenn jeder Administrator verschwunden wäre, funktioniert der Weg über die Kommandozeile von oben immer, weil er nur Zugriff auf den Server voraussetzt.

## Wie es weitergeht

- Sieh dir an, was das Panel kann, in @doc(instanceAdmin.panel).
- Durchstöbere die anderen Betreiberbefehle in @doc(selfHosting.cliCommands).
