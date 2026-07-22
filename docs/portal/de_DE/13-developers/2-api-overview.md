---
id: api.overview
title: API-Überblick
slug: api-ueberblick
section: entwickler
---

# API-Überblick

Die KolleK-API ist eine JSON-API, die die Webanwendung eins zu eins spiegelt. Jede Fähigkeit der App (Sammlungen erstellen, Objekte und Exemplare hinzufügen, Transaktionen erfassen, Mitglieder verwalten) hat einen passenden Endpunkt, durchgesetzt von genau denselben Regeln. Wenn deine Rolle dir etwas im Browser erlaubt, erlaubt es dir dein Token auch über HTTP. Wenn nicht, verweigert die API es auf dieselbe Weise wie die App.

Diese Seite gibt dir das mentale Modell. Die vollständige, immer aktuelle Endpunktreferenz wird aus dem Code generiert und von deiner Instanz ausgeliefert:

- `/docs/api` für die durchsuchbare Referenz.
- `/docs/api.md` für die gesamte Referenz als Markdown.
- `/docs/api/{section}.md` für einen einzelnen Abschnitt als Markdown, praktisch, um ein Thema in ein Werkzeug einzuspeisen.

:::note
Auf einer selbst gehosteten Instanz ist die Referenz Teil der öffentlichen Marketing-Website, die standardmäßig deaktiviert ist. Ein Betreiber schaltet sie mit der Einstellung `SHOW_MARKETING_SITE` ein. Siehe @doc(selfHosting.configure).
:::

## Auf dein Konto begrenzt

Die API ist mandantenbegrenzt. Ein Token gehört zu einem Benutzer, und ein Benutzer gehört zu genau einem @doc(accounts.usersAndRoles, "Konto"), also löst jede Anfrage über dieses Konto auf. Du kannst nicht auf die Daten eines anderen Kontos zugreifen, und du übergibst nirgendwo eine Konto-ID. Es gibt nichts zu konfigurieren: Authentifiziere dich, und du befindest dich in deinem eigenen Arbeitsbereich.

Dieselben @doc(accounts.usersAndRoles, "Rollen") gelten wie in der App. Das Token eines Betrachters kann lesen, aber nicht schreiben. Das Token eines Bearbeiters kann Katalog-Inhalte verwalten. Aktionen, die nur der Eigentümer ausführen darf (Mitglieder, Kontoeinstellungen), brauchen das Token eines Eigentümers.

## Wie die Ressourcen aufgebaut sind

Ressourcen sind so verschachtelt, wie @doc(kollek.howOrganized, "KolleK organisiert ist"):

- Dein **Konto** enthält kontoweite Ressourcen: Mitglieder, Sammlungstypen, benutzerdefinierte Felder, Tags, Standorte, Zustände.
- **Sammlungen** enthalten **Objekte**, zusammen mit Kategorien und Sets.
- **Objekte** enthalten **Fotos** und **Exemplare**.
- **Exemplare** tragen die Verlaufsressourcen: Transaktionen, Wertermittlungen, Versicherungseinträge, Leihgaben, Wartungseinträge, Provenienz-Ereignisse, Standortverlauf, Dokumente und die kombinierte Zeitleiste.

Antworten folgen lose der JSON:API-Form: Jede Ressource kommt als `type`, `id`, `attributes` und `links` zurück. Listen sind mit einem Standard-Umschlag paginiert, behandelt in @doc(api.rateLimitsAndConventions).

## Was dieser Abschnitt abdeckt

Diese Seiten behandeln den Einstieg und die Konzepte, die die generierte Referenz nicht vermitteln kann: Authentifizierung, Konventionen und den aktuellen Stand der Webhooks. Für einen bestimmten Endpunkt, seine Parameter und ausgearbeitete Anfrage- und Antwortbeispiele geh direkt zu `/docs/api`.

:::note
Es gibt keinen Testmodus. Jede API-Anfrage läuft gegen dein echtes Konto, sei also vorsichtig mit destruktiven Aufrufen, während du experimentierst.
:::

## Wie es weitergeht

- Stelle deine erste Anfrage in @doc(api.authenticate).
- Überflieg @doc(api.rateLimitsAndConventions), bevor du einen Client schreibst.
- Durchsuch die generierte Referenz unter `/docs/api` auf deiner Instanz.
