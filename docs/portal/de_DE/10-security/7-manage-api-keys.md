---
id: apiKeys.manage
title: API-Schlüssel verwalten
slug: api-schluessel-verwalten
section: sicherheit
---

# API-Schlüssel verwalten

Ein API-Schlüssel ist ein persönliches Token, das ein Skript oder eine Anwendung über die KolleK-API in deinem Namen handeln lässt. Diese Seite behandelt den Lebenszyklus: einen Schlüssel erstellen, ihn im Blick behalten und ihn widerrufen. Was du tatsächlich mit einem Schlüssel tun kannst, steht im @doc(api.authenticate, "Entwicklerbereich").

Planst du nie, die API zu nutzen, kannst du diese Seite komplett überspringen. Es gibt keine Schlüssel, bis du einen erstellst.

## Einen Schlüssel erstellen

::::steps
:::step title="Öffne deine API-Schlüssel-Einstellungen"
Gehe zu deinem Profil und öffne den Bereich für API-Schlüssel. Du siehst alle Schlüssel, die du bereits hast, jeweils mit dem Datum ihrer letzten Nutzung.
:::

:::step title="Den neuen Schlüssel benennen"
Wähle, einen Schlüssel zu erstellen, und gib ihm ein **Label**, das sagt, wofür er ist, etwa "Importskript" oder "Heim-Dashboard". Labels sind für dein zukünftiges Ich gedacht, das entscheidet, welchen Schlüssel es gefahrlos widerrufen kann.
:::

:::step title="Das Token sofort kopieren"
KolleK zeigt das Token einmalig, direkt nach der Erstellung. Kopiere es jetzt und bewahre es an einem sicheren Ort auf, etwa einem Passwort-Manager.

::screenshot{label="Neuer API-Schlüssel mit einmalig sichtbarem Token"}
:::
::::

:::warning
Das Token wird nur einmal angezeigt. Verlierst du es, kannst du es nicht erneut einsehen. Widerrufe den Schlüssel und erstelle einen neuen.
:::

KolleK schickt dir eine Benachrichtigung, sobald ein Schlüssel für deinen Benutzer erstellt wird, sodass ein unerwarteter Schlüssel nie unbemerkt bleibt.

## Deine Schlüssel im Blick behalten

Der Bereich für API-Schlüssel listet jeden Schlüssel mit seinem Label und wann er zuletzt genutzt wurde. Diese letzte Nutzungszeit ist dein Freund: Ein Schlüssel, der seit Monaten nicht genutzt wurde, ist ein Schlüssel, den du wahrscheinlich widerrufen kannst, und ein Schlüssel, der vor fünf Minuten genutzt wurde, obwohl dein Skript nicht gelaufen ist, ist ein Schlüssel, den du untersuchen solltest.

Eine Gewohnheit hält das überschaubar: ein Schlüssel pro Zweck. Hat jede Integration ihren eigenen Schlüssel, kannst du einen widerrufen, ohne die anderen zu beeinträchtigen.

## Einen Schlüssel widerrufen

Lösche den Schlüssel aus derselben Liste. Alles, was noch sein Token nutzt, funktioniert sofort nicht mehr, und KolleK schickt dir eine Benachrichtigung über die Löschung.

Widerrufe einen Schlüssel, wenn:

- Du das Skript oder die App, zu der er gehörte, nicht mehr nutzt.
- Das Token möglicherweise geleakt ist, etwa weil es in ein Repository eingecheckt oder in einem Chat geteilt wurde.
- Du eine @doc(security.alertEmails, "Warnung über einen erstellten oder gelöschten Schlüssel") erhalten hast, die du nicht wiedererkennst. In diesem Fall ändere auch dein Passwort.

:::note
Die Anmeldung über die API erstellt im Hintergrund ebenfalls ein Token. Diese Anmelde-Tokens lösen die E-Mail für erstellte Schlüssel nicht aus, sodass die Warnungen, die du erhältst, aussagekräftig bleiben.
:::

## Wie es weitergeht

- Setze einen Schlüssel mit deiner ersten Anfrage ein: @doc(api.authenticate).
- Verstehe die E-Mails rund um Schlüssel: @doc(security.alertEmails).
