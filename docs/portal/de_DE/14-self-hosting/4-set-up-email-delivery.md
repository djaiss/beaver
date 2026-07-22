---
id: selfHosting.setupEmailDelivery
title: E-Mail-Zustellung einrichten
slug: e-mail-zustellung-einrichten
section: self-hosting-uebersicht
---

# E-Mail-Zustellung einrichten

E-Mail ist der Weg, wie KolleK Personen außerhalb einer Browsersitzung erreicht: @doc(collaboration.invitePeople, "Einladungen"), @doc(auth.magicLinks, "Magic Links"), Passwort-Zurücksetzungen, E-Mail-Verifizierung und @doc(security.alertEmails, "Sicherheitswarnungen") kommen alle per E-Mail an. Solange du die Zustellung nicht konfigurierst, geht keine davon irgendwohin.

## Der Standard versendet nichts

Eine frische Instanz wird mit `MAIL_MAILER=log` ausgeliefert. Jede E-Mail wird in die Anwendungslogdatei geschrieben, statt versendet zu werden. Das ist Absicht: So versendet eine halb konfigurierte Instanz nie unbemerkt Mails von einer falschen Adresse, und du kannst beim Testen genau nachlesen, was versendet worden wäre.

:::note
Wenn jemand bei einer neuen Instanz sagt "Ich habe die Einladung nie bekommen", liegt das fast immer an diesem Standardverhalten. Die E-Mail existiert, in der Logdatei. Siehe @doc(troubleshooting.emailDelivery).
:::

Es gibt zwei unterstützte Wege, echte E-Mails zu versenden: einen beliebigen SMTP-Server oder den Dienst Resend.

## Option 1: SMTP

::::steps
:::step title="Mailer und Serverdetails festlegen"
Setze in `.env`:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

Jeder Transaktions-E-Mail-Anbieter oder selbst betriebene Mailserver mit SMTP-Zugangsdaten funktioniert.
:::

:::step title="Absenderidentität festlegen"
Lege die Adresse und den Namen fest, die deine Benutzer sehen:

```bash
MAIL_FROM_ADDRESS=kollek@yourdomain.com
MAIL_FROM_NAME="KolleK"
```

Verwende eine Domain, die du kontrollierst und für den Versand konfiguriert hast (SPF und DKIM bei deinem Anbieter), sonst landen deine Mails im Spam.
:::

:::step title="Anwenden und testen"
Erstelle die Container neu und löse dann eine echte E-Mail aus, zum Beispiel indem du auf der Anmeldeseite einen Magic Link anforderst:

```bash
docker compose up -d
```
:::
::::

## Option 2: Resend

Wenn du [Resend](https://resend.com) nutzt, setze:

```bash
USE_RESEND=true
RESEND_API_KEY=re_your_api_key
```

E-Mails werden dann über die API von Resend statt per SMTP versendet, und jeder Versand vermerkt die Resend-Nachrichten-ID.

## Prüfen, ob die Zustellung funktioniert

KolleK protokolliert jede versendete E-Mail, pro Benutzer, mit Betreff, Inhalt und Zustellstatus. Prüfe nach deinem Test zwei Stellen:

- Dein Postfach, aus offensichtlichem Grund.
- Die Seite **Gesendete E-Mails** des Empfängers in seinem Profil, die auflistet, was die Instanz ihm geschickt hat. Siehe @doc(activity.logAndSentEmails, "Dein persönliches Aktivitätsprotokoll und gesendete E-Mails").

Häufige Anzeichen für Probleme:

- **Nichts kommt an, aber es gibt auch keinen Fehler.** Der Mailer steht noch auf `log`. Prüfe, ob `.env` durch das Neuerstellen der Container übernommen wurde.
- **E-Mails werden versendet, landen aber im Spam.** Die Absenderdomain ist nicht authentifiziert. Richte SPF und DKIM bei deinem Anbieter ein.
- **Versandfehler im Log.** Zugangsdaten oder Hostdetails sind falsch. Die Logs des Queue-Workers enthalten die Fehlermeldung des Anbieters.

E-Mails werden über die Hintergrund-Warteschlange versendet, der Container **queue** muss also laufen, damit überhaupt etwas die Instanz verlässt.

## Wie es weitergeht

- Lerne die E-Mails kennen, die deine Instanz versendet, in @doc(reference.emailsSent).
- Diagnostiziere Zustellprobleme in @doc(troubleshooting.emailDelivery).
