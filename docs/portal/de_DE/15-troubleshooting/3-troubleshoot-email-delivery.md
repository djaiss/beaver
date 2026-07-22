---
id: troubleshooting.emailDelivery
title: Probleme beim E-Mail-Versand beheben
slug: probleme-beim-e-mail-versand-beheben
section: fehlerbehebung-und-faq
---

# Probleme beim E-Mail-Versand beheben

Du hast jemanden eingeladen, und nichts kam an. Du hast einen Magic Link angefordert, und dein Postfach bleibt leer. Diese Seite erklärt, warum erwartete E-Mails verschwinden und wie du herausfindest, was tatsächlich passiert ist.

## Die häufigste Ursache: Eine frische Instanz versendet keine E-Mails

Auf einer neu selbst gehosteten Instanz protokolliert KolleKs Mailer standardmäßig **E-Mails, statt sie zu versenden**. Jede E-Mail wird erstellt und aufgezeichnet, aber nichts verlässt den Server, bis ein Betreiber einen echten Mailversand-Dienst konfiguriert.

Das ist Absicht, damit eine unkonfigurierte Instanz nie still versagt oder versehentlich spammt. Es bedeutet aber, dass auf einer frischen Installation Einladungen, Magic Links, Passwortzurücksetzungen und Sicherheitswarnungen allesamt scheinbar verschwinden.

:::note
Hat noch niemand den Mailversand auf deiner Instanz konfiguriert, kommt keine E-Mail an, für niemanden, nie. Das ist die erste Sache, die du prüfen solltest.
:::

**Betreibst du die Instanz selbst**, richte SMTP oder Resend ein, indem du @doc(selfHosting.setupEmailDelivery) folgst.

**Betreibt sie jemand anderes**, verweise diese Person auf die Seite. Von innerhalb der App aus kannst du nichts ändern.

## Prüfe, was tatsächlich versendet wurde

KolleK erfasst jede E-Mail, die es an dich sendet, mit ihrem Zustellstatus. Geh zu deinem Profil und öffne deine **gesendeten E-Mails**-Historie. Jeder Eintrag zeigt, wann sie versendet wurde, und, wo Tracking verfügbar ist, ob sie zugestellt oder abgewiesen wurde.

So liest du, was du findest:

- **Die E-Mail ist gelistet und als zugestellt markiert.** KolleK hat seinen Job gemacht. Prüfe deinen Spam-Ordner und durchsuche dein Postfach nach der Absenderadresse.
- **Die E-Mail ist gelistet und als abgewiesen markiert.** Dein Mail-Anbieter hat sie zurückgewiesen. Prüfe, ob deine Adresse in deinem Profil korrekt ist, und ob dein Anbieter die Instanz blockiert.
- **Die E-Mail ist gelistet, aber ohne Zustellinformationen.** Auf Instanzen, die über einfaches SMTP versenden, ist Zustell-Tracking nicht verfügbar, das ist also normal. Keine Abweisung ist ein gutes Zeichen.
- **Die E-Mail ist überhaupt nicht gelistet.** Sie wurde nie erstellt, was meist bedeutet, dass die Aktion nicht abgeschlossen wurde. Versuche die Aktion erneut.

Ausführliche Details zu diesem Bildschirm in @doc(activity.logAndSentEmails, "Dein persönliches Aktivitätsprotokoll und gesendete E-Mails").

## Eine Einladung hat die eingeladene Person nie erreicht

Die Einladungs-E-Mail geht an die eingeladene Person, sie erscheint also nie in deiner eigenen Sendehistorie. Bitte die eingeladene Person, den Spam-Ordner zu prüfen, verifiziere, dass du die Adresse richtig eingegeben hast, und denk daran, dass Einladungen nach sieben Tagen ablaufen. Im Zweifel, sende eine neue. Auf einer frischen Instanz, prüfe zuerst die Mailer-Konfiguration wie oben.

## Verifizierung, Zurücksetzungen und Magic Links landen im Spam

Transaktionale E-Mails von einer kleinen, selbst gehosteten Instanz sind genau das, wogegen Spamfilter misstrauisch sind. Eine Nachricht als "kein Spam" zu markieren, schult meist deinen Anbieter. Betreiber können die Zustellbarkeit mit korrekter Absenderkonfiguration verbessern, behandelt in @doc(selfHosting.setupEmailDelivery).

## Wie geht es weiter

- Betreiber-Einrichtung für echten Versand: @doc(selfHosting.setupEmailDelivery).
- Deine persönliche E-Mail-Historie: @doc(activity.logAndSentEmails, "Dein persönliches Aktivitätsprotokoll und gesendete E-Mails").
- Was jede E-Mail ist und wann sie ausgelöst wird: @doc(reference.emailsSent).
