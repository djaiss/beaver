---
id: troubleshooting.signIn
title: Probleme bei der Anmeldung beheben
slug: probleme-bei-der-anmeldung-beheben
section: fehlerbehebung-und-faq
---

# Probleme bei der Anmeldung beheben

Ausgesperrt, oder tut etwas auf der Anmeldeseite nicht das, was du erwartet hast? Finde dein Symptom unten. Jeder Eintrag gibt zuerst die Lösung, dann Links zur ausführlicheren Erklärung.

## Ich habe mein Passwort vergessen

Nutze den Link **Passwort vergessen** auf der Anmeldeseite. Gib deine E-Mail-Adresse ein, öffne die Zurücksetzungs-E-Mail und wähle ein neues Passwort. Der Zurücksetzungslink läuft nach 60 Minuten ab, nutze ihn also zeitnah und fordere einen neuen an, falls er verfällt.

Schnellere Alternative: Fordere stattdessen einen @doc(auth.magicLinks, "Magic Link") an. Er meldet dich ohne Passwort an, und du kannst danach von deinem Profil aus ein neues Passwort setzen.

Ausführliche Details in @doc(auth.resetPassword).

## Mein neues Passwort wird immer wieder abgelehnt

KolleK verlangt mindestens acht Zeichen und lehnt jedes Passwort ab, das in einem öffentlichen Datenleck aufgetaucht ist. Die Ablehnung betrifft das Passwort selbst, nicht dein Konto. Wähle etwas Längeres und Einzigartiges, das du nirgendwo sonst verwendet hast. Siehe @doc(auth.resetPassword).

## Ich habe mein Zwei-Faktor-Gerät verloren

Gib bei der Zwei-Faktor-Abfrage einen deiner **Wiederherstellungscodes** anstelle des sechsstelligen Codes ein. Jeder Wiederherstellungscode funktioniert einmal. Bist du wieder drin, deaktiviere und reaktiviere die Zwei-Faktor-Authentifizierung mit deinem neuen Gerät, um eine frische Kopplung und einen frischen Satz Codes zu bekommen.

Ausführliche Details in @doc(security.recoveryCodes).

:::warning
Hast du deinen Authentifikator verloren und keine Wiederherstellungscodes mehr, gibt es keinen selbstständigen Weg, den Zwei-Faktor-Schritt abzuschließen. Auf einer selbst gehosteten Instanz wende dich an die Person, die deinen Server betreibt.
:::

## Mein Magic Link funktioniert nicht

Magic Links sind **fünf Minuten** gültig und funktionieren **einmal**. Ist deiner abgelaufen oder bereits benutzt, fordere einen neuen von der Anmeldeseite an. Stelle sicher, dass du den Link auf dem Gerät öffnest, auf dem du angemeldet sein möchtest.

Ausführliche Details in @doc(auth.magicLinks).

## Ich habe es zu oft versucht und bin jetzt gesperrt

Wiederholte, schnelle Versuche werden gedrosselt, um Passwort-Raten zu verlangsamen. Warte eine Minute und versuche es dann sorgfältig erneut. Bist du dir beim Passwort unsicher, wechsle zum @doc(auth.resetPassword, "Zurücksetzungsablauf") oder einem @doc(auth.magicLinks, "Magic Link"), statt weiter zu raten.

## Ich habe eine E-Mail "fehlgeschlagene Anmeldung" bekommen, die ich nicht kenne

Jemand hat deine E-Mail-Adresse mit einem falschen Passwort eingegeben. Siehe @doc(security.alertEmails), was das bedeutet und wann du handeln solltest.

## Mein Einladungslink funktioniert nicht

Zwei häufige Ursachen:

- **Die Einladung ist abgelaufen.** Einladungen sind sieben Tage gültig. Bitte den Kontoeigentümer um eine neue.
- **Deine E-Mail-Adresse hat bereits einen KolleK-Benutzer.** Eine Person gehört zu genau einem Konto, sodass eine Einladung nicht von einer E-Mail-Adresse angenommen werden kann, die bereits ein eigenes Konto hat.

Ausführliche Details in @doc(collaboration.invitePeople).

## Die E-Mail, auf die ich warte, kommt nie an

Die Zurücksetzungs-E-Mail, der Magic Link oder die Einladung erreicht dich vielleicht nicht. Das ist meist ein Zustellungsproblem, kein Anmeldeproblem. Siehe @doc(troubleshooting.emailDelivery).

## Wie geht es weiter

- Die Grundlagen jedes Anmeldewegs: @doc(auth.signIn).
- Härte die Dinge ab, sobald du wieder drin bist: @doc(security.index).
