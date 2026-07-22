---
id: dataSafety.howProtected
title: Wie deine Daten geschützt werden
slug: wie-deine-daten-geschuetzt-werden
section: kernkonzepte
---

# Wie deine Daten geschützt werden

Ein Katalog erfasst, was du besitzt, was es wert ist und wo es aufbewahrt wird. Das ist von Natur aus sensibel, und KolleK behandelt es entsprechend. Diese Seite erklärt die Schutzmaßnahmen in verständlichen Worten und ist ehrlich darüber, wo sie enden.

## Verschlüsselt bei der Speicherung

Sensible Felder (Namen, Objektdetails, Werte und vieles mehr) werden mit dem Verschlüsselungsschlüssel der Instanz in der Datenbank verschlüsselt gespeichert. Jemand, der eine Kopie der Datenbankdatei ohne den Schlüssel erhielte, würde die sensiblen Spalten unlesbar vorfinden.

Das geschieht automatisch. Als Benutzer musst du dafür nichts einschalten oder konfigurieren.

## Jede Änderung wird erfasst

KolleK führt ein Audit-Protokoll der Benutzeraktionen. Wenn Sam ein Objekt bearbeitet, zeigt der Eintrag, wer es getan hat, was sich geändert hat und wann, und er speist den Aktivitäts-Feed des Kontos sowie das eigene Protokoll jedes Objekts. Der Name der handelnden Person wird zum Zeitpunkt der Aktion erfasst, sodass die Historie auch dann lesbar bleibt, wenn dieser Benutzer später gelöscht wird. Siehe @doc(activity.feedAndAuditTrail).

## Die ehrliche Grenze

:::note
Verschlüsselung bei der Speicherung schützt den gespeicherten Datenbankinhalt. Das ist keine Ende-zu-Ende-Verschlüsselung. Die Anwendung kann deine Daten lesen, um sie dir anzuzeigen, und wer auch immer die Instanz betreibt, hält den Verschlüsselungsschlüssel.
:::

In der Praxis bedeutet das: Dein Vertrauen folgt dem Betreiber. @doc(selfHosting.index, "Hostest du selbst"), bist du dieser Betreiber, und du hältst den Schlüssel auf deiner eigenen Hardware. Hostet jemand anderes KolleK für dich, hält diese Person technisch den Schlüssel, genau wie bei jeder gehosteten Webanwendung.

Zwei Folgen sind wissenswert:

- **Der Schlüssel ist kostbar.** Geht er verloren, kann niemand die verschlüsselten Daten wiederherstellen. Betreiber sollten @doc(selfHosting.applicationKeyAndEncryption) lesen.
- **Backups zählen.** Verschlüsselung schützt vor Schnüffeln, nicht vor Verlust. Self-Hoster sollten @doc(selfHosting.backupAndRestore) folgen.

## Was du kontrollierst

Du entscheidest, was das Konto verlässt. Heute verlässt es nichts: Keine Sammlung ist von außerhalb deines Kontos überhaupt erreichbar. Jede Sammlung trägt eine @doc(sharing.overview, "Sichtbarkeitseinstellung"), die festhält, für wen sie gedacht ist, und wenn Teilen verfügbar wird, wird eine als öffentlich markierte Sammlung die einzige Fläche, die ein Fremder je zu sehen bekommt.

## Wie geht es weiter

- Sieh, wer was geändert hat, in @doc(activity.feedAndAuditTrail).
- Härte deine eigene Anmeldung mit @doc(security.index).
- Betreibst du deine eigene Instanz? Lies @doc(selfHosting.applicationKeyAndEncryption).
