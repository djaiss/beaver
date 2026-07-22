---
id: activity.logAndSentEmails
title: Dein Aktivitätsprotokoll und gesendete E-Mails
slug: aktivitaetsprotokoll-und-gesendete-e-mails
section: konto-und-profil
---

# Dein Aktivitätsprotokoll und gesendete E-Mails

KolleK führt zwei Aufzeichnungen über dich, die du jederzeit einsehen kannst: alles, was du getan hast, und jede E-Mail, die das System dir geschickt hat. Beide liegen in deinem Profilbereich, und beide existieren aus demselben Grund: Transparenz. Fragst du dich "habe ich das wirklich geändert" oder "wurde diese Magic-Link-E-Mail überhaupt verschickt", findest du die Antwort hier.

## Dein Aktivitätsprotokoll

Das @doc(activity.feedAndAuditTrail, "Aktivitätsprotokoll"), das sich durch das ganze Konto zieht, hat eine persönliche Ansicht: eine vollständige Historie deiner eigenen Aktionen, vom Erstellen eines Objekts bis zum Ändern einer Einstellung. Öffne sie über deinen Profilbereich.

Nutze sie, um deine Schritte nachzuvollziehen. Sieht der Standort eines Exemplars falsch aus, zeigt dir dein Protokoll, ob und wann du es umgezogen hast.

## Deine gesendeten E-Mails

KolleK erfasst jede E-Mail, die es dir schickt: Magic Links, Einladungen, die du erhalten hast, Verifizierungsnachrichten und @doc(security.alertEmails, "Sicherheitswarnungen"). Dein Profilbereich listet sie auf, neueste zuerst, zehn pro Seite.

Jeder Eintrag zeigt, was gesendet wurde und wann. Meldet der Mailversand der Instanz eine Rückmeldung, siehst du außerdem, ob die Nachricht zugestellt wurde oder unzustellbar war.

Diese Liste ist der schnellste Weg, fehlende E-Mails zu untersuchen:

- **Die E-Mail erscheint hier, kam aber nie in deinem Postfach an.** Prüfe deinen Spam-Ordner und ob der Eintrag eine Unzustellbarkeit zeigt.
- **Die E-Mail erscheint hier gar nicht.** Die Aktion, die sie hätte auslösen sollen, ist nicht passiert, fordere sie also erneut an.
- **E-Mails erscheinen hier, aber keine wird je zugestellt.** Auf einer selbst gehosteten Instanz bedeutet das meist, dass der Mailversand noch nicht eingerichtet ist. Verweise deinen Betreiber auf @doc(selfHosting.setupEmailDelivery, "den Mailversand einrichten").

:::note
Diese Seite zeigt E-Mails, die an dich gesendet wurden. Sie ist persönlich, wie der Rest deines Profils, und andere Mitglieder können deine Liste nicht durchsuchen.
:::

## Wie es weitergeht

- Verstehe die kontoweite Historie in @doc(activity.feedAndAuditTrail).
- Fehlt dir eine erwartete E-Mail? Arbeite dich durch @doc(troubleshooting.emailDelivery).
