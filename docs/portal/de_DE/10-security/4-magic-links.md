---
id: auth.magicLinks
title: Magic Links erklärt
slug: magic-links
section: sicherheit
---

# Magic Links erklärt

Ein Magic Link ist ein passwortloser Weg, dich anzumelden. Statt dein Passwort einzutippen, bittest du KolleK, dir einen Link per E-Mail zu schicken. Öffne den Link, und du bist angemeldet. Diese Seite erklärt, wie das funktioniert, wann es praktisch ist, und den einen Kompromiss, den du kennen solltest, bevor du dich darauf verlässt.

## Einen Magic Link anfordern

Wähle auf der Anmeldeseite die Magic-Link-Option, gib deine **E-Mail-Adresse** ein und sende sie ab. KolleK schickt einen einmaligen Link an diese Adresse. Öffne ihn, und du landest auf deinem Dashboard.

Zu deinem Schutz zeigt die Seite dieselbe Bestätigung, egal ob für die eingegebene Adresse ein Konto existiert oder nicht, sodass sie nie verrät, wer registriert ist.

## Die Regeln, denen er folgt

- **Der Link ist fünf Minuten gültig.** Läuft er ab, bevor du ihn öffnest, fordere einen neuen an. Es geht nichts verloren.
- **Er geht nur an die E-Mail-Adresse deines Kontos.** Du brauchst Zugriff auf dieses Postfach. Das ist auch, was den Link sicher macht: Nur wer deine E-Mails lesen kann, kann ihn nutzen.
- **Er funktioniert einmal.** Ein Link, der dich bereits angemeldet hat, kann nicht erneut verwendet werden.

## Der Kompromiss mit der Zwei-Faktor-Authentifizierung

Die Anmeldung über einen Magic Link fragt nicht nach einem @doc(security.twoFactorAuth, "Zwei-Faktor")-Code.

Das ist Absicht, kein Versehen. Ein Magic Link beweist bereits zwei Dinge gleichzeitig: dass die sich anmeldende Person deine E-Mail-Adresse kennt, und dass sie das dahinterliegende Postfach kontrolliert. Das Postfach übernimmt die Rolle des zweiten Faktors.

:::warning
Nutzt du die Zwei-Faktor-Authentifizierung, denk daran, dass jeder, der dein E-Mail-Postfach kontrolliert, sich mit einem Magic Link bei KolleK anmelden kann, ohne je deinen Authenticator zu sehen. Dein E-Mail-Konto ist das eigentliche Tor, schütze es also mit einem starken Passwort und einer eigenen Zwei-Faktor-Einrichtung.
:::

## Wann du ihn nutzen solltest

Magic Links passen zu dir, wenn:

- Du an einem Gerät bist, an dem du dein Passwort nicht eintippen willst.
- Du dein Passwort vergessen hast und einfach nur reinkommen musst. Bist du drin, kannst du in deinem Profil ein @doc(auth.resetPassword, "neues Passwort festlegen").
- Du im Alltag lieber kein Passwort nutzt und dein E-Mail-Konto gut geschützt ist.

Bevorzuge dein Passwort und deinen Authenticator-Code, wenn du an einem gemeinsam genutzten oder nicht vertrauenswürdigen Rechner bist, an dem du dein Postfach lieber gar nicht erst öffnen willst.

## Wie es weitergeht

- Jeder Anmeldeweg an einem Ort: @doc(auth.signIn).
- Stärke die Eingangstür: @doc(security.twoFactorAuth).
- Link nie angekommen? Siehe @doc(troubleshooting.emailDelivery).
