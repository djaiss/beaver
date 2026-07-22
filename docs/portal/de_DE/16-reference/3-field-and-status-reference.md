---
id: reference.fieldAndStatus
title: Feld- und Statusreferenz
slug: feld-und-statusreferenz
section: referenz
---

# Feld- und Statusreferenz

Jede Optionsliste, der du in einem KolleK-Formular begegnest, an einem überschaubaren Ort. Jede Gruppe verlinkt zur Anleitung, die sie verwendet. Definitionen der Begriffe selbst findest du im @doc(reference.glossary, "Glossar").

## Exemplarstatus

Wird für jedes erfasste Exemplar gesetzt. Verwendet in @doc(copies.track).

| Status | Bedeutung |
| --- | --- |
| Im Besitz | Du hast dieses Exemplar. Der Standardwert für ein neues Exemplar. |
| Bestellt | Gekauft oder reserviert, auf dem Weg zu dir. |
| Verliehen | Gerade bei jemand anderem. Die Verwahrung hat sich geändert, das Eigentum nicht. |
| Verkauft | Du hast es verkauft und besitzt es nicht mehr. |
| Verschenkt | Du hast es weggegeben. |
| Verloren | Du kannst es nicht finden und erwartest es nicht wiederzufinden. |
| Gestohlen | Dir gestohlen worden. |
| Entsorgt | Weggeworfen oder recycelt, mit optionalem Entsorgungsdatum. |
| Sonstiges | Alles, was die obige Liste nicht abdeckt. |

:::note
Im Besitz, Bestellt und Verliehen zählen als weiterhin gehalten. Ein verliehenes Exemplar gehört dir immer noch, es ist nur woanders.
:::

## Transaktionstypen

Wird für jede Transaktion gesetzt. Verwendet in @doc(copies.recordPaymentsAndValue). Als "erwerbend" markierte Typen bringen ein Exemplar in deinen Besitz, und die früheste erwerbende Transaktion liefert das Erwerbsdatum des Exemplars.

| Typ | Bedeutung |
| --- | --- |
| Kauf | Du hast das Exemplar gekauft. Erwerbend. |
| Verkauf | Du hast das Exemplar verkauft. |
| Tausch | Du hast etwas dagegen eingetauscht. Erwerbend. |
| Geschenk erhalten | Jemand hat es dir geschenkt. Erwerbend. |
| Geschenk gegeben | Du hast es jemandem geschenkt. |
| Erbschaft | Es ist an dich übergegangen. Erwerbend. |
| Rückerstattung | Geld, das für eine frühere Transaktion zurückerstattet wurde. |
| Gebühr | Zusätzliche Kosten rund um das Exemplar, zum Beispiel eine Auktionsgebühr. |
| Steuer | Eine für das Exemplar gezahlte Steuer. |
| Versand | Separat erfasste Versandkosten. |
| Sonstiges | Jedes Geldereignis, das die Liste nicht abdeckt. |

## Wertermittlungstypen und Vertrauensgrad

Wird für jede Wertermittlung gesetzt. Verwendet in @doc(copies.recordPaymentsAndValue).

| Wertermittlungstyp | Bedeutung |
| --- | --- |
| Eigene Schätzung | Deine eigene Einschätzung des Werts. |
| Professionelle Begutachtung | Eine formelle Begutachtung durch eine Fachperson. |
| Marktschätzung | Abgeleitet aus aktuellen Markt- oder Verkaufsdaten. |
| Versicherungswert | Der für Versicherungszwecke verwendete Wert. |
| Auktionsschätzung | Eine von einem Auktionshaus angegebene Schätzung. |
| Automatische Schätzung | Erstellt von einem Preisdienst oder -werkzeug. |
| Sonstiges | Jede andere Grundlage für den Wert. |

| Vertrauensgrad | Bedeutung |
| --- | --- |
| Niedrig | Eine grobe Schätzung. |
| Mittel | Einigermaßen fundiert. |
| Hoch | Gut belegt, zum Beispiel durch eine aktuelle professionelle Begutachtung. |
| Unbekannt | Der Vertrauensgrad wurde nicht erfasst. |

## Versicherungsstatus

Wird für jeden Versicherungseintrag gesetzt. Verwendet in @doc(copies.insure). Die Deckungsart eines Versicherungseintrags ist Freitext und hat daher keine feste Optionsliste.

| Status | Bedeutung |
| --- | --- |
| Aktiv | Die Police deckt das Exemplar derzeit ab. |
| Abgelaufen | Der Deckungszeitraum ist zu Ende. |
| Storniert | Die Police wurde vor ihrem Enddatum storniert. |
| Ausstehend | Der Versicherungsschutz ist arrangiert, aber noch nicht in Kraft. |

## Leihrichtungen und -status

Wird für jede Leihgabe gesetzt. Verwendet in @doc(loans.lendAndBorrow).

| Richtung | Bedeutung |
| --- | --- |
| Verliehen | Dein Exemplar hat deine Hände verlassen, zum Beispiel an einen Freund oder eine Ausstellung. |
| Geliehen | Das Stück einer anderen Person befindet sich bei dir. |

| Status | Bedeutung |
| --- | --- |
| Geplant | Vereinbart, aber noch nicht übergeben. |
| Aktiv | Das Exemplar ist derzeit unterwegs (oder bei dir). |
| Überfällig | Immer noch unterwegs, obwohl das Fälligkeitsdatum überschritten ist. KolleK markiert das automatisch jeden Tag. |
| Zurückgegeben | Die Leihgabe ist beendet, und das Exemplar ist zurückgekommen. |
| Storniert | Die Leihgabe kam nie zustande. |
| Verloren | Das Exemplar ist nicht zurückgekommen. |

## Wartungstypen

Wird für jeden Wartungseintrag gesetzt. Verwendet in @doc(copies.recordMaintenance).

| Typ | Bedeutung |
| --- | --- |
| Reinigung | Routinemäßige Reinigung. |
| Reparatur | Beheben von Schäden. |
| Service | Regelmäßige Pflege, zum Beispiel ein Uhrenservice. |
| Konservierung | Arbeit zur Stabilisierung und Erhaltung. |
| Restaurierung | Arbeit, um das Exemplar in einen früheren Zustand zurückzuversetzen. |
| Austausch | Ersetzen eines Teils oder einer Komponente. |
| Inspektion | Eine Prüfung ohne Eingriff. |

## Provenienz-Ereignistypen und Datumsgenauigkeit

Wird für jedes Provenienz-Ereignis gesetzt. Verwendet in @doc(copies.traceProvenance).

| Ereignistyp | Bedeutung |
| --- | --- |
| Erwerb | Das Exemplar kam in eine Sammlung. |
| Verkauf | Das Exemplar wurde verkauft. |
| Geschenk | Das Exemplar wechselte als Geschenk den Besitzer. |
| Erbschaft | Das Exemplar ging durch einen Nachlass. |
| Eigentumsübertragung | Das Eigentum wechselte auf andere Weise. |
| Verwahrungsübertragung | Das Exemplar wechselte den Ort, ohne den Eigentümer zu wechseln. |
| Leihgabe | Das Exemplar ging als Leihgabe hinaus. |
| Rückgabe | Das Exemplar kam von einer Leihgabe zurück. |
| Ausstellung | Das Exemplar wurde öffentlich gezeigt. |
| Authentifizierung | Das Exemplar wurde als echt bestätigt. |
| Begutachtung | Das Exemplar wurde formell bewertet. |
| Bedeutende Restaurierung | Größere Arbeit, die zur Geschichte gehört. |
| Ursprung | Wo und wann das Exemplar hergestellt wurde. |
| Entdeckung | Das Exemplar wurde gefunden oder wiederentdeckt. |
| Sonstiges | Jedes andere Kapitel der Geschichte. |

Provenienzdaten sind oft unsicher, daher trägt jedes Ereignis eine Genauigkeitsangabe:

| Genauigkeit | Bedeutung |
| --- | --- |
| Genaues Datum | Das vollständige Datum ist bekannt. |
| Monat | Bekannt bis auf den Monat. |
| Jahr | Bekannt bis auf das Jahr. |
| Ungefähr | Eine bestmögliche Schätzung. Lies es als "circa". |
| Unbekannt | Kein Datum erfasst. |

## Dokumenttypen

Wird für jedes Dokument gesetzt. Verwendet in @doc(copies.attachDocuments).

| Typ | Bedeutung |
| --- | --- |
| Quittung | Kaufbeleg. |
| Rechnung | Eine Rechnung für das Exemplar oder Arbeiten daran. |
| Zertifikat | Ein Zertifikat, das mit dem Exemplar mitgeliefert wurde. |
| Begutachtung | Eine schriftliche Wertermittlung. |
| Versicherung | Versicherungsunterlagen. |
| Foto | Ein Foto, das als Beleg aufbewahrt wird, nicht als Galeriebild. |
| Zustandsbericht | Eine schriftliche Zustandsbewertung. |
| Restaurierungsbericht | Eine Aufzeichnung der Restaurierungsarbeit. |
| Katalog | Ein Katalogeintrag oder eine Auflistung. |
| Korrespondenz | Briefe oder E-Mails zum Exemplar. |
| Eigentumsnachweis | Unterlagen, die das Eigentum belegen. |
| Echtheitsnachweis | Unterlagen, die belegen, dass das Exemplar echt ist. |
| Sonstiges | Alles andere, das sich zu behalten lohnt. |

## Typen für benutzerdefinierte Felder

Wird beim Definieren eines benutzerdefinierten Felds an einem Sammlungstyp gewählt. Verwendet in @doc(collectionTypes.setup).

| Feldtyp | Bedeutung |
| --- | --- |
| Text | Freitext, zum Beispiel Autor oder Verlag. |
| Zahl | Ein numerischer Wert, zum Beispiel eine Ausgabennummer. |
| Datum | Ein Kalenderdatum, zum Beispiel ein Erscheinungsdatum. |
| Ja / Nein | Ein Kontrollkästchen, zum Beispiel "Signiert". |
| Auswahl | Eine Wahl aus einer von dir definierten Optionsliste. |
| Bewertung | Eine Sternebewertung, bis zu fünf Sterne. |

## Sichtbarkeit von Sammlungen

Wird für jede Sammlung gesetzt. Verwendet in @doc(collections.share). Die Einstellung wird heute schon erfasst und greift, sobald das Teilen verfügbar ist. Siehe @doc(troubleshooting.featureStatus).

| Sichtbarkeit | Bedeutung |
| --- | --- |
| Privat | Nur für dich gedacht. |
| Geteilt | Für alle im Konto gedacht. |
| Öffentlich | Für jeden mit dem Link gedacht, nur lesend, ohne Anmeldung. |

## Wie es weitergeht

- Was die Begriffe bedeuten: @doc(reference.glossary).
- Die Einträge, zu denen diese Optionen gehören: @doc(copyHistory.concept, "Die Geschichte eines Exemplars erklärt").
