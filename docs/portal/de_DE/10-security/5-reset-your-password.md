---
id: auth.resetPassword
title: Setze dein Passwort zurück
slug: passwort-zuruecksetzen
section: sicherheit
---

# Setze dein Passwort zurück

Egal ob du dein Passwort vergessen hast oder einfach ein neues willst, diese Seite behandelt beide Wege: den Zugriff über die Anmeldeseite wiederherstellen und dein Passwort bewusst über dein Profil ändern.

## Wenn du dein Passwort vergessen hast

1. Wähle auf der Anmeldeseite den Link **Passwort vergessen**.
2. Gib deine E-Mail-Adresse ein und sende sie ab.
3. Öffne die E-Mail, die KolleK dir schickt, und folge dem Link zum Zurücksetzen.
4. Wähle ein neues Passwort und bestätige es. Du kannst dich jetzt damit anmelden.

Zwei Verhaltensweisen solltest du kennen, damit sie dich nicht verwirren:

- **Die Bestätigungsmeldung ist immer dieselbe**, egal ob für die eingegebene Adresse ein Konto existiert oder nicht. Das schützt deine Privatsphäre, indem es nie verrät, wer registriert ist. Hast du ein Konto, kommt die E-Mail an.
- **Der Link zum Zurücksetzen läuft nach 60 Minuten ab.** Öffnest du ihn zu spät, fordere einfach einen weiteren an.

:::note
Möchtest du das Zurücksetzen ganz überspringen, kann dich ein @doc(auth.magicLinks, "Magic Link") ohne Passwort anmelden. Bist du drin, kannst du in deinem Profil ein neues Passwort festlegen.
:::

## Wenn du es einfach nur ändern willst

Du brauchst den Ablauf für vergessene Passwörter nicht, um dein Passwort zu wechseln. Gehe zu deinem Profil, öffne den Sicherheitsbereich und ändere dort dein Passwort. Du gibst dein aktuelles Passwort ein und wählst das neue.

## Warum ein Passwort abgelehnt werden könnte

KolleK prüft jedes neue Passwort gegen zwei Regeln, sodass eine Ablehnung nie ein Rätsel ist:

- **Mindestens acht Zeichen.** Kürzere Passwörter werden von vornherein abgelehnt.
- **Keine bekannten, geleakten Passwörter.** Dein gewünschtes Passwort wird gegen Listen von Passwörtern geprüft, die bei öffentlichen Datenlecks aufgetaucht sind. Ist es je irgendwo geleakt worden, wird es abgelehnt, selbst wenn es stark wirkt. Dabei geht es um das Passwort selbst, nicht um dein Konto, wähle also etwas, das du auf keiner anderen Seite verwendet hast.

Ein Passwort-Manager umgeht beide Regeln mühelos, indem er etwas Langes und Einzigartiges erzeugt.

## Wie es weitergeht

- Füge einen zweiten Schritt hinzu, damit ein gestohlenes Passwort nicht reicht: @doc(security.twoFactorAuth).
- Kommst du immer noch nicht rein? Arbeite dich durch @doc(troubleshooting.signIn).
- E-Mail zum Zurücksetzen nie angekommen? Siehe @doc(troubleshooting.emailDelivery).
