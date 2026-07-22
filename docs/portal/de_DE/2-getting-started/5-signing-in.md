---
id: auth.signIn
title: Anmelden
slug: anmelden
section: erste-schritte
---

# Anmelden

KolleK bietet dir mehrere Wege, dich anzumelden. Diese Seite geht jeden davon durch, damit du wählen kannst, was zu dir passt, und zeigt dir, wohin du dich wendest, wenn du ausgesperrt bist.

## Mit E-Mail und Passwort anmelden

Der übliche Weg. Gehe zur Anmeldeseite, gib die **E-Mail-Adresse** und das **Passwort** ein, mit denen du dich registriert hast, und sende das Formular ab. Du landest auf deinem Dashboard.

Wenn @doc(security.twoFactorAuth, "Zwei-Faktor-Authentifizierung") für dein Konto aktiviert ist, wirst du direkt nach deinem Passwort nach einem Code gefragt. Siehe unten.

## Mit einem Magic Link anmelden

Wenn du lieber kein Passwort eintippen möchtest, kann KolleK dir einen Link per E-Mail schicken, der dich anmeldet.

Wähle auf der Anmeldeseite die Magic-Link-Option, gib deine **E-Mail-Adresse** ein und sende das Formular ab. KolleK schickt an diese Adresse einen einmaligen Link. Öffne ihn, und du bist angemeldet.

Zwei Dinge solltest du wissen:

- **Der Link ist fünf Minuten lang gültig.** Läuft er ab, fordere einfach einen neuen an.
- **Der Link geht an die E-Mail-Adresse deines Kontos**, du brauchst also Zugriff auf dieses Postfach. Genau das macht ihn sicher: Nur wer deine E-Mails lesen kann, kann ihn verwenden.

## Der Zwei-Faktor-Schritt

Wenn du die Zwei-Faktor-Authentifizierung aktiviert hast, kommt beim Anmelden mit deinem Passwort ein zusätzlicher Schritt dazu. Nachdem dein Passwort akzeptiert wurde, fragt KolleK nach dem aktuellen Code aus deiner Authenticator-App. Gib ihn ein, um die Anmeldung abzuschließen.

Falls du deinen Authenticator nicht erreichen kannst, kannst du stattdessen einen deiner @doc(security.recoveryCodes, "Wiederherstellungscodes") eingeben. Jeder Wiederherstellungscode funktioniert einmal.

:::warning
Bei der Anmeldung mit einem Magic Link wird kein Zwei-Faktor-Code abgefragt, weil der Zugriff auf dein E-Mail-Postfach bereits als zweiter Faktor gilt. Wenn du dich auf die Zwei-Faktor-Authentifizierung verlässt, denk daran, wenn du dich für einen Anmeldeweg entscheidest, und schütze dein E-Mail-Konto entsprechend.
:::

Wie du die Zwei-Faktor-Authentifizierung einrichtest und Wiederherstellungscodes speicherst, wird im Abschnitt **Security** dieser Dokumentation behandelt.

## Passwort vergessen

Wenn du dich an dein Passwort nicht erinnerst, nutze den Link "Passwort vergessen" auf der Anmeldeseite. Gib deine E-Mail-Adresse ein, und KolleK schickt dir einen Link zum Zurücksetzen.

Zu deinem Schutz zeigt KolleK immer dieselbe Bestätigungsmeldung an, egal ob für diese Adresse ein Konto existiert oder nicht, sodass die Seite nicht verrät, wer registriert ist. Wenn du ein Konto hast, kommt die E-Mail zum Zurücksetzen an. Wenn du dich mit einem Magic Link wieder anmeldest, kannst du dein Passwort danach über dein Profil zurücksetzen.

## Wie es weitergeht

- Neu hier und noch am Einrichten? Kehre zurück zu @doc(gettingStarted.checklist).
- Willst du besseren Schutz? Aktiviere die Zwei-Faktor-Authentifizierung im Abschnitt **Security**.
