---
id: instanceAdmin.panel
title: Das Instanzadministrationspanel
slug: instanzadministrationspanel
section: self-hosting-uebersicht
---

# Das Instanzadministrationspanel

Das Instanzadministrationspanel unter `/instance-admin` ist der Ort, an dem ein @doc(instanceAdmin.grantAccess, "Instanzadministrator") über alle Konten des Servers hinweg schaut: wie viele es gibt, wer darin ist, und die Handvoll destruktiver Aktionen, die nur ein Betreiber innehaben sollte. Diese Seite beschreibt, was das Panel kann, und ebenso wichtig, was es bewusst nicht kann.

Wenn du eine private Instanz mit einem einzigen Konto betreibst, brauchst du dieses Panel vielleicht nie. Es lohnt sich auf gemeinsam genutzten Instanzen, etwa einem Vereins- oder Familienserver mit mehreren Konten.

:::note
Das Panel erscheint nur für Benutzer mit dem Instanzadministrator-Flag. Wer sonst `/instance-admin` aufruft, bekommt eine Seite "nicht gefunden", nicht "Zugriff verweigert", sodass das Panel seine Existenz nie verrät.
:::

## Die Übersicht

Das Panel öffnet sich mit einer Übersicht über die gesamte Instanz:

- Anzahl von **Konten**, **Benutzern**, **Sammlungen** und **Objekten** über den ganzen Server hinweg.
- **Diesen Monat erstellte Konten** und **diesen Monat aktive Benutzer**, sodass du siehst, ob die Instanz wächst oder ruhig ist.
- Ein Diagramm der **Anmeldungen pro Monat** über die letzten zwölf Monate.

Diese Zahlen sind instanzweit. Sie geben keinen Einblick in den Inhalt der Kataloge einzelner Personen.

## Konten durchsuchen

Der Bereich **Konten** listet jedes Konto der Instanz auf, 25 pro Seite, mit der Mitgliederzahl und Sammlungszahl jedes Kontos.

Du kannst Konten **nach der E-Mail-Adresse eines Mitglieds** suchen und nach Rolle filtern. Die Suche nach Konto- oder Personennamen ist nicht möglich, da Namen in der Datenbank verschlüsselt sind und dort nicht abgeglichen werden können. Die E-Mail-Adresse ist der verlässliche Ansatzpunkt.

Öffnest du ein Konto, siehst du dessen Mitglieder, sortiert erst nach Eigentümern, dann Bearbeitern, dann Betrachtern, zusammen mit den Sammlungs- und Objektzahlen des Kontos und seinen fünfzehn jüngsten Einträgen im Aktivitätsprotokoll.

## Die destruktiven Aktionen

Drei Aktionen im Panel ändern oder entfernen Daten, und keine davon lässt sich rückgängig machen:

- **Ein Konto löschen**, wodurch das Konto mit jeder Sammlung, jedem Objekt, jedem Exemplar, jedem Mitglied und der gesamten Historie darin entfernt wird.
- **Einen Benutzer löschen**, wodurch diese Person aus ihrem Konto entfernt wird.
- **Das Administrator-Flag eines anderen Benutzers umschalten**, wodurch jemand anderem die Instanzadministration gewährt oder entzogen wird.

:::warning
Das Löschen eines Kontos oder Benutzers über dieses Panel geschieht sofort und endgültig. Nichts durchläuft den Papierkorb, und es gibt keine Wiederherstellung. Prüfe zweimal, ob du das richtige Konto oder die richtige Person ausgewählt hast, bevor du bestätigst.
:::

Zwei Schutzmechanismen bewahren die Instanz vor sich selbst: Ein Administrator kann sich sein eigenes Flag nicht entziehen und seinen eigenen Benutzer nicht über das Panel löschen. Wie auch immer es genutzt wird, die Instanz behält mindestens einen funktionierenden Administrator.

## Was das Panel nicht ist

Das Panel ist bewusst nur im Web verfügbar. Die JSON-API ist auf ein einzelnes Konto beschränkt, und eine instanzweite Oberfläche hat dort keinen Platz, also existiert keine dieser Fähigkeiten als API-Endpunkt.

Die im Panel sichtbaren Bereiche **Support** und **Bewertungen** sind Platzhalter und noch nicht umgesetzt. Siehe @doc(troubleshooting.featureStatus).

## Wie es weitergeht

- Vergib oder entziehe das Flag selbst in @doc(instanceAdmin.grantAccess).
- Verstehe, was Kontoeigentümer bereits ohne dich tun können, in @doc(collaboration.manageMembersAndRoles).
- Sieh dir die anderen Betreiberwerkzeuge in @doc(selfHosting.cliCommands) an.
