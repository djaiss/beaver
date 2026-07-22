---
id: troubleshooting.faq
title: Häufig gestellte Fragen
slug: haeufig-gestellte-fragen
section: fehlerbehebung-und-faq
---

# Häufig gestellte Fragen

Kurze Antworten auf die Fragen, die immer wieder auftauchen. Jede verlinkt auf die Seite, die das Thema ausführlich behandelt.

## Was ist der Unterschied zwischen einem Objekt und einem Exemplar?

Ein Objekt ist die Art von Ding, wie "Amazing Spider-Man #1". Ein Exemplar ist eine physische Ausgabe, die du tatsächlich besitzt. Besitzt du drei Exemplare desselben Comics, ist das ein Objekt mit drei Exemplaren, jedes mit eigenem Zustand, Standort, Wert und Historie. Das ist die wichtigste einzelne Idee in KolleK. Siehe @doc(items.itemsVsCopies).

## Kann ich zu mehr als einem Konto gehören?

Nein. Ein Benutzer gehört zu genau einem Konto, und eine E-Mail-Adresse kann nur einen Benutzer haben. Das bedeutet auch, dass eine Einladung zum Konto einer anderen Person nicht von einer E-Mail-Adresse angenommen werden kann, die bereits ihr eigenes Konto hat. Siehe @doc(accounts.usersAndRoles).

## Ist KolleK wirklich kostenlos?

Ja. Es gibt überhaupt keine Bezahlung innerhalb der App: keine Pläne, keine Stufen, keine hinter einer Bezahlschranke versteckten Funktionen. Self Hosting ist kostenlos, und jede Funktion ist enthalten, egal wie du es betreibst. Siehe @doc(kollek.hostingOptions).

## Wie bekomme ich meine Daten heraus?

Heute kannst du innerhalb der App @doc(collectionTypes.importExport, "Sammlungstyp-Definitionen als JSON exportieren"). Es gibt noch keinen Export für Objekte oder ganze Sammlungen. Die vollständige Antwort für Self-Hoster ist ein Backup der Datenbank und hochgeladenen Dateien auf Instanzebene, behandelt in @doc(selfHosting.backupAndRestore). Die ehrliche Zusammenfassung steht in @doc(dataSafety.backupCollectionData).

## Warum kann ich den letzten Eigentümer nicht entfernen oder degradieren?

Ein Konto muss immer mindestens einen Eigentümer behalten, sonst könnte niemand es verwalten, Mitglieder einladen oder es löschen. Befördere zuerst jemand anderen zum Eigentümer. Siehe @doc(collaboration.manageMembersAndRoles).

## Wo ist die Suchfunktion?

Die Suche über alles hinweg vom Dashboard aus ist noch nicht verfügbar; die Leiste, die du dort siehst, ist ein Platzhalter. Was heute funktioniert: das Filtern innerhalb einer geöffneten Sammlung und die Suche in deiner Fotobibliothek. Siehe @doc(troubleshooting.featureStatus).

## Funktionieren Webhooks schon?

Zur Hälfte. Du kannst Endpunkte registrieren, und jeder erhält ein Signaturgeheimnis, aber noch kein Anwendungsereignis löst einen Webhook aus. Die Zustellmechanik ist bereit; die Ereignisse kommen, sobald das Produkt wächst. Siehe @doc(webhooks.overview).

## Sind meine Daten verschlüsselt, und was schützt das?

Sensible Felder werden mit dem Schlüssel deiner Instanz in der Datenbank verschlüsselt gespeichert. Das schützt den Datenbankinhalt, falls allein die Datenbank gestohlen wird. Es ist keine Ende-zu-Ende-Verschlüsselung: Wer auch immer die Instanz betreibt, hält den Schlüssel und kann auf die Daten zugreifen. Siehe @doc(dataSafety.howProtected).

## Kann ich eigene Zustände hinzufügen?

Ja. Öffne **Objektzustände** in den Kontoeinstellungen, um Zustände hinzuzufügen, umzubenennen oder zu löschen, einschließlich der vorgegebenen (Neu, Wie neu, Gebraucht, Abgenutzt, Beschädigt). Siehe @doc(conditions.manage).

## Etwas wurde gelöscht. Kann ich es zurückbekommen?

War es eine Sammlung, ein Objekt, ein Exemplar, eine Kategorie oder ein Set, landete es im Papierkorb und kann standardmäßig 30 Tage lang wiederhergestellt werden. Fotos, Dokumente und Verlaufseinträge werden sofort entfernt und können nicht von innerhalb der App aus wiederhergestellt werden. Siehe @doc(dataSafety.restoreFromTrash).

## Immer noch nicht weiter?

- Anmeldeprobleme: @doc(troubleshooting.signIn).
- Fehlende E-Mails: @doc(troubleshooting.emailDelivery).
- Was fertig ist und was nicht: @doc(troubleshooting.featureStatus).
