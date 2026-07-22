---
id: webhooks.overview
title: Webhooks
slug: webhooks
section: entwickler
---

# Webhooks

Webhooks lassen ein externes System einen HTTP-Aufruf von KolleK erhalten, wenn in deinem Konto etwas passiert. Du kannst sie schon heute einrichten, und diese Seite zeigt wie. Aber lies zuerst den nächsten Absatz, denn er gibt den Rahmen für alles Weitere vor.

:::note
Derzeit löst kein Anwendungsereignis einen Webhook aus. Die Maschinerie für Registrierung, Signierung und Zustellung ist vorhanden und getestet, aber Ereignisse werden erst ausgelöst, sobald der Sammlungsbereich wächst. Richte deinen Empfänger jetzt gern schon ein, warte nur noch nicht darauf, dass etwas ankommt. Die @doc(troubleshooting.featureStatus, "Seite zum Funktionsstatus") verfolgt, wann sich das ändert.
:::

## Was heute schon existiert

Das Registrieren eines Endpunkts speichert eine Ziel-URL mit ihrem eigenen Signierungsgeheimnis. Wenn KolleK irgendwann Ereignisse auslöst, wird jedes an alle aktiven Endpunkte zugestellt, die du registriert hast, signiert, damit dein Empfänger prüfen kann, dass es wirklich von deiner Instanz stammt.

Webhook-Endpunkte gehören zu deinem Benutzer, nicht zum gesamten Konto.

## Einen Endpunkt registrieren

Öffne in der App deine Profileinstellungen und gehe zu **Webhooks**. Füge die URL hinzu, auf der dein Empfänger lauscht, mit einem Label, damit du weißt, wofür sie ist. Jeder Endpunkt erhält sein eigenes Signierungsgeheimnis, eine 64 Zeichen lange Zeichenfolge, die bei der Erstellung des Endpunkts generiert wird. Bewahre sie bei deinem Empfänger auf.

Ein Betreiber kann einen Endpunkt auch über die Kommandozeile erstellen:

```bash
php artisan kollek:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

Der Befehl gibt die Endpunkt-ID und ihr Signierungsgeheimnis aus.

## Die Nutzlast, die dein Empfänger erwarten sollte

Jede Zustellung ist ein JSON-`POST` mit dieser Form:

```json
{
  "event": "example.event",
  "happened_at": "2026-07-20T14:30:00+00:00",
  "data": {}
}
```

- `event` benennt, was passiert ist. Es sind noch keine Ereignisnamen definiert.
- `happened_at` ist ein ISO-8601-Zeitstempel des Zeitpunkts, an dem es passiert ist.
- `data` trägt die Nutzlast für dieses Ereignis.

## Signaturen überprüfen

Jede Zustellung enthält einen `Signature`-Header: einen HMAC-SHA256-Hash des rohen Anfragekörpers, berechnet mit dem Signierungsgeheimnis deines Endpunkts. Berechne denselben Hash auf deiner Seite neu und vergleiche. Weichen sie voneinander ab, verwirf die Anfrage, denn sie stammt nicht von deiner Instanz.

```php
$computed = hash_hmac('sha256', $rawRequestBody, $signingSecret);

if (! hash_equals($computed, $request->header('Signature'))) {
    abort(401);
}
```

## Zustellung und Wiederholungsversuche

Zustellungen werden in eine Warteschlange gestellt und im Hintergrund gesendet. Eine fehlgeschlagene Zustellung wird bis zu 3 Mal mit exponentiellem Backoff wiederholt. Dein Empfänger sollte schnell mit einem 2xx-Status antworten und seine eigentliche Arbeit asynchron erledigen.

Auf einer selbst gehosteten Instanz laufen Zustellungen über den Queue-Worker, die Queue-Rolle muss also laufen. Siehe @doc(selfHosting.installDocker).

## Wie es weitergeht

- Prüfe, was live ist und was noch aussteht, auf der @doc(troubleshooting.featureStatus, "Seite zum Funktionsstatus").
- Entwickle in der Zwischenzeit gegen die API, beginnend mit @doc(api.authenticate).
