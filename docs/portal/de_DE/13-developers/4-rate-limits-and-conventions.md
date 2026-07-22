---
id: api.rateLimitsAndConventions
title: Ratenlimits und Konventionen
slug: ratenlimits-und-konventionen
section: entwickler
---

# Ratenlimits und Konventionen

Eine Handvoll Konventionen gelten für die gesamte API. Sie einmal zu lernen erspart dir Überraschungen bei jedem Endpunkt, deshalb stehen sie hier, statt in der Referenz wiederholt zu werden.

## Ratenlimits

- Authentifizierte Anfragen sind pro Benutzer auf **60 pro Minute** begrenzt.
- `POST /api/register` und `POST /api/login` sind auf **6 pro Minute** begrenzt, was vor Credential Stuffing schützt.

Überschreitest du ein Limit, antwortet die API mit HTTP 429. Warte einen Moment und versuche es erneut. Wenn du einen Massenimport schreibst, drossle deine Anfragen, statt sie so schnell wie möglich abzufeuern, und denk daran, dass die API pro Anfrage ein Objekt verarbeitet, da es keine Massen-Endpunkte gibt.

## Paginierung

Listen-Endpunkte sind paginiert und teilen sich einen Umschlag:

- `data` enthält die Seite mit Ressourcen.
- `links` enthält die URLs `first`, `last`, `prev` und `next`.
- `meta` enthält die aktuelle Seite, die Gesamtanzahl und weitere Details.

Seiten enthalten standardmäßig **10 Ressourcen**. Fordere mehr mit dem Query-Parameter `per_page` an, bis zu einem **Maximum von 100**. Folge `links.next`, bis es `null` ist, um eine ganze Liste zu durchlaufen.

## Geldbeträge in der kleinsten Währungseinheit

Jeder Betrag in der API (geschätzte Werte, Transaktionsbeträge, Anzahlungen, Versicherungswerte) ist eine Ganzzahl in der kleinsten Einheit seiner Währung. Für Dollar und Euro bedeutet das Cent: Ein Kauf von 49,99 € wird als `4999` übertragen. Das vermeidet Rundungsfehler bei Fließkommazahlen vollständig. Rechne für die Anzeige in deinem eigenen Code um, und denk daran, dass jede @doc(collections.overview, "Sammlung") ihre eigene Währung trägt.

## Verboten wird als nicht gefunden gemeldet

Die API setzt dieselben @doc(accounts.usersAndRoles, "Rollen") durch wie die Web-App, mit einer bewussten Besonderheit: eine Aktion, die dir nicht erlaubt ist, oder eine Ressource in einem anderen Konto, antwortet mit **404 Not Found**, nicht 403 Forbidden. Ein Aufrufer kann "das existiert nicht" nicht von "das gehört dir nicht" unterscheiden, deshalb bestätigt die API nie, was außerhalb deines Kontos existiert.

:::note
Wenn ein Endpunkt bei einem Objekt, das du in der App sehen kannst, unerwartet 404 zurückgibt, prüfe die Rolle des Benutzers, dessen Token du verwendest. Das Token eines Betrachters bekommt bei jedem Schreibvorgang 404.
:::

## Fehler und Validierung

Fehlgeschlagene Validierung antwortet mit HTTP 422, einer `message` und einem `errors`-Objekt, das nach Feldnamen geordnet ist. Andere Fehler folgen der üblichen HTTP-Semantik: 401, wenn das Token fehlt oder widerrufen wurde, 404 wie oben beschrieben, 429 bei Ratenlimits.

## Wie es weitergeht

- Sieh dir diese Konventionen an echten Endpunkten in der generierten Referenz unter `/docs/api` an.
- Bereit für die Ereigniszustellung eines Tages? Lies, wo @doc(webhooks.overview) heute stehen.
