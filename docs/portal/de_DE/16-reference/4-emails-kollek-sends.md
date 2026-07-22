---
id: reference.emailsSent
title: "E-Mails, die KolleK versendet"
slug: e-mails-die-kollek-versendet
section: referenz
---

# E-Mails, die KolleK versendet

Jede E-Mail, die das System versenden kann, was sie auslöst, und wer sie erhält. Nutze diese Seite, um eine legitime Nachricht zu erkennen, oder um die Zustellung zu prüfen, wenn du eine Instanz betreibst.

KolleK führt auf deiner @doc(activity.logAndSentEmails, "Seite mit gesendeten E-Mails") Buch über jede E-Mail, die es dir schickt, einschließlich Zustellungs- und Bounce-Status. Betreiber, die noch keinen Mailer konfiguriert haben, sollten @doc(selfHosting.setupEmailDelivery) lesen, denn eine frische Instanz protokolliert E-Mails nur, ohne sie zu versenden.

## Zugang bekommen und behalten

| E-Mail | Ausgelöst wenn | Gesendet an |
| --- | --- | --- |
| Kontoeinladung | Ein Eigentümer lädt jemanden zum Konto ein. Der Einladungslink läuft nach sieben Tagen ab. | Die eingeladene Adresse |
| Magic Link | Jemand fordert einen passwortlosen Anmeldelink an. Der Link ist fünf Minuten lang gültig. | Die E-Mail-Adresse des Kontos |
| E-Mail-Bestätigung | Du registrierst dich oder änderst deine E-Mail-Adresse. | Die neue Adresse |
| Passwort zurücksetzen | Du nutzt den Link "Passwort vergessen". Der Reset-Link ist 60 Minuten lang gültig. | Die E-Mail-Adresse des Kontos |

## Sicherheitshinweise

Diese treffen unaufgefordert ein, wenn etwas Bemerkenswertes an deinem Konto passiert. Siehe @doc(security.alertEmails) für das weitere Vorgehen, falls dich eine überrascht.

| E-Mail | Ausgelöst wenn | Gesendet an |
| --- | --- | --- |
| Warnung bei fehlgeschlagener Anmeldung | Ein Anmeldeversuch mit Passwort bei einem bestehenden Konto schlägt fehl. | Die E-Mail-Adresse des Kontos |
| Warnung bei neuer Anmeldung | Eine erfolgreiche Anmeldung findet statt, mit Angabe des verwendeten Geräts. | Die E-Mail-Adresse des Kontos |
| Warnung bei IP-Adressänderung | Eine Anmeldung erfolgt von einer anderen IP-Adresse als beim letzten Mal. | Die E-Mail-Adresse des Kontos |
| API-Schlüssel erstellt | Du erstellst manuell einen API-Schlüssel. Tokens, die durch Anmeldung über die API entstehen, lösen diesen Hinweis nicht aus. | Die E-Mail-Adresse des Kontos |
| API-Schlüssel gelöscht | Du löschst einen API-Schlüssel. | Die E-Mail-Adresse des Kontos |

## Hinweise an den Betreiber

Diese gehen an die auf der Instanz konfigurierte Betreiberadresse, nicht an Sammler. Sie existieren, damit die Person, die den Server betreibt, weiß, wenn Leute gehen.

| E-Mail | Ausgelöst wenn | Gesendet an |
| --- | --- | --- |
| Benutzer gelöscht | Eine Person löscht ihren eigenen Benutzer, einschließlich des angegebenen Grundes. | Die Betreiberadresse |
| Benutzer automatisch gelöscht | Das System löscht einen Benutzer, der der Löschung bei Inaktivität zugestimmt hat und seit sechs Monaten inaktiv ist. | Die Betreiberadresse |

## Wie es weitergeht

- Die Hinweise erkennen und darauf reagieren: @doc(security.alertEmails).
- E-Mail-Versand auf deiner Instanz tatsächlich zum Laufen bringen: @doc(selfHosting.setupEmailDelivery).
- Prüfen, was an dich gesendet wurde: @doc(activity.logAndSentEmails, "Dein persönliches Aktivitätsprotokoll und gesendete E-Mails").
