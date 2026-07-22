---
id: selfHosting.addLanguage
title: Eine Sprache hinzufügen
slug: eine-sprache-hinzufuegen
section: self-hosting-uebersicht
---

# Eine Sprache hinzufügen

KolleK gibt es in sieben Sprachen: Englisch, Französisch, Spanisch, Deutsch, brasilianisches Portugiesisch, vereinfachtes Chinesisch und Japanisch. Jeder Benutzer wählt seine eigene Sprache in seinem Profil und kann sie sogar von der Anmeldeseite aus wechseln. Diese Seite erklärt, wie Übersetzungen im Hintergrund funktionieren, und wie ein Betreiber oder Mitwirkender ein neues Sprachgebiet hinzufügt oder ein bestehendes vervollständigt.

Wenn du nur die Sprache ändern möchtest, die du selbst siehst, brauchst du nichts davon. Siehe @doc(profile.changeLanguage).

## Wie Übersetzungen gespeichert werden

Jedes Sprachgebiet ist eine JSON-Datei unter `lang/`, benannt nach dem Sprachcode, zum Beispiel `lang/fr_FR.json`. Jede Datei bildet den ursprünglichen englischen String auf seine Übersetzung ab. Die Liste der Sprachgebiete, die die App anbietet, ist in der Anwendungskonfiguration als unterstützte Sprachgebiete definiert.

## Ein Sprachgebiet anlegen oder auffrischen

Der Befehl `kollek:localize` durchsucht die gesamte Anwendung nach übersetzbaren Strings und gleicht sie mit der Datei eines Sprachgebiets ab:

```
php artisan kollek:localize fr_FR
```

Strings, die seit dem letzten Lauf neu hinzugekommen sind, werden hinzugefügt, und Strings, die nicht mehr existieren, werden entfernt. In der englischen Datei ist jeder String seine eigene Übersetzung, Englisch ist also per Definition immer vollständig. In jedem anderen Sprachgebiet kommen neue Strings leer an, bereit für einen Übersetzer, sie auszufüllen.

Eine ganz neue Sprache hinzuzufügen läuft genauso ab: Registriere das Sprachgebiet in der Konfiguration der unterstützten Sprachgebiete, führe den Befehl mit dem neuen Sprachcode aus, um seine Datei zu erzeugen, und übersetze dann die leeren Einträge.

:::note
Eine leere Übersetzung fällt auf Englisch zurück, statt die Oberfläche zu zerstören, sodass ein teilweise übersetztes Sprachgebiet nutzbar bleibt, während die Arbeit weitergeht.
:::

## Was noch nicht übersetzt ist

Die eingeloggte Anwendung ist vollständig übersetzbar. Die öffentliche Marketingseite und die generierte API-Referenz sind noch nicht übersetzt und werden immer auf Englisch angezeigt, unabhängig vom Sprachgebiet des Besuchers. Siehe @doc(troubleshooting.featureStatus).

## Wie es weitergeht

- Führe den Befehl auf deiner Instanz aus mit @doc(selfHosting.cliCommands).
- Sieh dir die Sicht der Leser dazu an in @doc(profile.changeLanguage).
