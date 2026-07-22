---
id: tutorials.trackValuableItem
title: "Tutorial: Verfolge das ganze Leben eines wertvollen Objekts"
slug: wertvolles-objekt-verfolgen
section: tutorials
---

# Tutorial: Verfolge das ganze Leben eines wertvollen Objekts

Die meisten Objekte brauchen einen Zustand, einen Standort und vielleicht einen Preis. Ein wirklich wertvolles Objekt verdient mehr: einen Beleg dafür, was du bezahlt hast, eine professionelle Einschätzung seines Werts, eine Versicherung, die Unterlagen, die all das belegen, und eine Aufzeichnung, wohin es überall gereist ist und was alles mit ihm gemacht wurde. KolleK erfasst jedes davon als eigenen datierten Eintrag beim Exemplar, und dieses Tutorial durchläuft alle davon an einem einzigen Stück.

Wir begleiten dabei Priya, die gerade die beste Uhr ihrer Sammlung gekauft hat, einen Chronografen von 1968. Am Ende trägt das Exemplar eine Transaktion, eine Wertermittlung, einen Versicherungseintrag, zwei Dokumente, eine abgeschlossene Leihgabe, einen Wartungseintrag und eine Herkunftserzählung, alles lesbar als eine einzige Zeitleiste.

Das ist das längste Tutorial. Mach es mit einem echten Objekt, das dir gehört, oder lies einfach mit, um zu sehen, wie die Teile zusammenpassen.

## Bevor du beginnst

- Beende zuerst @doc(tutorials.catalogueFirstCollection, "Katalogisiere deine erste Sammlung von Anfang bis Ende"). Dieses Tutorial setzt voraus, dass dir der Kernablauf in Fleisch und Blut übergegangen ist.
- Lies @doc(copyHistory.concept, "Die Historie eines Exemplars erklärt"). Sie ist die Landkarte für alles Folgende.
- Behalte die beiden Regeln im Kopf, die das Modell zusammenhalten: Geld lebt immer nur in Transaktionen, und eine Neubewertung oder Neuversicherung schreibt einen neuen Eintrag, statt den alten zu überschreiben.

## Schritt 1: Das Objekt und sein Exemplar katalogisieren

Priya erstellt das Objekt "Heuer Carrera 2447" in ihrer Sammlung Watches, die den fertigen Typ **Watches** verwendet. Sie füllt die Felder des Typs aus: **Brand**, **Model**, **Movement** (Automatic, Quartz oder Manual) und beantwortet **Box & Papers** mit Ja.

Dann fügt sie das Exemplar hinzu, und ein Feld ist hier wichtiger als sonst:

- **Identifier.** Sie trägt die Seriennummer der Uhr ein. Bei wertvollen Objekten ist das die Verbindung deines Datensatzes zum physischen Gegenstand, genauso wie eine Slab-Nummer bei einem begutachteten Comic.
- **Zustand** und **Standort**, wie immer.

Alles Folgende passiert im Reiter **Historie** dieses Exemplars, der jeweils ein Exemplar zeigt.

## Schritt 2: Den Erwerb erfassen

::::steps
:::step title="Die Kauftransaktion hinzufügen"
Füge in der Historie des Exemplars eine **Transaktion** vom Typ **Purchase** hinzu. Priya trägt den Betrag ein, das Auktionshaus als **Gegenpartei**, das **Datum**, das Aufgeld unter **Gebühren** und die Losnummer als **Referenz**.

::screenshot{label="Ausgefülltes Transaktionsformular für einen Auktionskauf"}
:::
::::

Warum das wichtig ist: Dieser eine Eintrag gibt dem Exemplar seinen bezahlten Preis und sein Erwerbsdatum, verankert die Statistik und wird später die Herkunftserzählung verankern. Bekommst du ihn richtig hin, hängt alles Weitere daran. Die Details stehen in @doc(copies.recordPaymentsAndValue).

## Schritt 3: Eine professionelle Wertermittlung hinzufügen

Priya lässt die Uhr schätzen. Sie fügt eine **Wertermittlung** mit dem Typ **Professional appraisal** hinzu, den geschätzten Betrag, die Zuverlässigkeit auf **High** gesetzt und den Namen des Gutachters als Person, die den Wert ermittelt hat.

:::note
Nächstes Jahr lässt sie die Uhr erneut schätzen und fügt eine neue Wertermittlung hinzu. Die alte bleibt bestehen. Der geschätzte Wert des Exemplars ist immer seine jüngste Wertermittlung, und die Abfolge der Wertermittlungen ist es, mit der du eines Tages den Wertverlauf über die Zeit darstellen kannst.
:::

## Schritt 4: Die Uhr versichern

Mit einer professionellen Schätzung in der Hand ist eine Versicherung der naheliegende nächste Schritt. Priya fügt einen @doc(copies.insure, "Versicherungseintrag") hinzu: den **Versicherer**, den **versicherten Wert**, die **Policennummer**, die **Deckungsart**, die **Selbstbeteiligung**, das **Start- und Enddatum**, ob es sich um ein **gelistetes Objekt** in der Police handelt, und die Kontaktdaten des Versicherers. Sie lässt den Status auf **Active**.

Wenn die Police erneuert wird, fügt sie einen neuen Eintrag hinzu und markiert diesen als **Expired**. Abgelaufene und gekündigte Einträge bleiben als abgeblendete Historie hinter dem aktuellen sichtbar, was genau das ist, was du brauchst, wenn eine Schadensmeldung fragt, welcher Versicherungsschutz in einem bestimmten Jahr bestand.

## Schritt 5: Die Unterlagen anhängen

Einträge sind Behauptungen. Dokumente sind der Beleg. Priya scannt zwei Papiere und @doc(copies.attachDocuments, "hängt sie dort an"), wo sie hingehören:

::::steps
:::step title="Die Quittung an die Transaktion anhängen"
Bei der Kauftransaktion hängt sie die Auktionsrechnung als Dokument vom Typ **Receipt** an, mit Ausstellungsdatum und Rechnungsnummer als Referenz.
:::

:::step title="Das Gutachten an die Wertermittlung anhängen"
Bei der Wertermittlung hängt sie den Bericht des Gutachters als Dokument vom Typ **Appraisal** an.
:::
::::

Ein Dokument kann eine hochgeladene Datei sein (PDF, Bilder, Word, Excel, CSV oder reiner Text, bis zu 12 MB) oder ein externer Link, falls die Unterlagen anderswo liegen. Jedes Dokument an den Eintrag anzuhängen, den es belegt, statt lose an das Exemplar, ist es, was die Geschichte später überprüfbar macht.

## Schritt 6: Die Uhr an eine Ausstellung verleihen und zurückbekommen

Ein lokaler Uhrmacherverein bittet darum, die Uhr einen Monat lang auszustellen. Der Gewahrsam ist genau das, was @doc(loans.lendAndBorrow, "Leihgaben") nachverfolgen.

::::steps
:::step title="Die ausgehende Leihgabe erfassen"
Priya erstellt eine **Leihgabe** mit der Richtung **Lent out**, dem Verein als Partei, "Exhibition" als Zweck, dem Leih- und Fälligkeitsdatum und dem Zustand der Uhr, als sie ihre Hände verließ.
:::

:::step title="Den geänderten Status des Exemplars sehen"
Solange die Leihgabe offen ist, zeigt das Exemplar als verliehen an. Es gehört weiterhin ihr, nur der Gewahrsam hat gewechselt, nicht das Eigentum. Wäre das Fälligkeitsdatum ohne Rückgabe verstrichen, würde KolleK die Leihgabe automatisch als überfällig kennzeichnen.
:::

:::step title="Die Rückgabe erfassen"
Als die Uhr zurückkommt, erfasst sie die **Rückgabe**, die das Rückgabedatum und den Zustand bei der Rückkehr festhält. Der Vergleich des Zustands beim Verleihen und bei der Rückgabe macht Transportschäden sichtbar statt strittig.
:::
::::

## Schritt 7: Die Wartung protokollieren

Bevor die Uhr ausgestellt wurde, ließ Priya sie warten. Sie fügt einen @doc(copies.recordMaintenance, "Wartungseintrag") vom Typ **Servicing** hinzu: einen Titel, den Uhrmacher, der die Arbeit ausgeführt hat, das Datum, die Kosten, den Zustand vorher und nachher, und ein **nächstes Fälligkeitsdatum** fünf Jahre in der Zukunft, damit die App den nächsten fälligen Service anzeigen kann, wenn er näher rückt. Da ein vollständiger Service an einem antiken Uhrwerk bedeutsam ist, entscheidet sie sich, ihn in die Herkunft des Exemplars aufzunehmen.

## Schritt 8: Die Herkunftserzählung aufbauen

Zum Schluss die Eigentumsgeschichte. Priya kennt die Vergangenheit der Uhr aus dem Auktionskatalog und erfasst sie als @doc(copies.traceProvenance, "Herkunftsereignisse"), älteste zuerst:

- Ein **Origin**-Ereignis für ihre Herstellung, datiert auf das Jahr 1968.
- Eine **Ownership transfer** an die Familie des ursprünglichen Besitzers, mit der Datumsgenauigkeit auf **Approximate** gesetzt, weil der Katalog nur "circa 1975" angibt.
- Ein **Exhibition**-Ereignis für die gerade abgeschlossene Ausstellung beim Verein.
- Ihre eigene **Acquisition**, exakt datiert, verknüpft mit der Kauftransaktion aus Schritt 2.

Zwei Dinge sind zu beachten. Datumsgenauigkeit existiert, weil Herkunft oft unsicher ist. Ein Ereignis kann exakt, auf den Monat, auf das Jahr, annähernd oder gar nicht datiert sein, und wird entsprechend angezeigt. Und Herkunftsereignisse tragen keine Beträge: Ein Ereignis, das mit einem Kauf oder Verkauf verknüpft ist, verweist auf dessen Transaktion, sodass Geld an genau einer Stelle bleibt.

## Schritt 9: Die ganze Geschichte lesen

Öffne die **Zeitleiste** des Exemplars. Alles, was du gerade erfasst hast, der Kauf, die Wertermittlung, die Versicherung, die Dokumente, das Verleihen und die Rückgabe, die Wartung und die Herkunftsereignisse, liest sich als eine einzige chronologische Geschichte. Die Standardansicht beschränkt sich auf die bedeutsamen Einträge, die vollständige Ansicht fügt die routinemäßigen hinzu. @doc(copyHistory.readTimeline) erklärt die Ansicht vollständig.

Das ist der Lohn: ein Bildschirm, der beantwortet, was die Uhr gekostet hat, was sie wert ist, wer sie besessen hat, was mit ihr gemacht wurde, und was all das belegt.

## Häufige Fehler, die du vermeiden solltest

- **Den Kaufpreis als Wertermittlung erfassen.** Es ist eine Transaktion. Diese Unterscheidung ist das Rückgrat des gesamten Modells.
- **Alte Einträge bearbeiten statt neue hinzuzufügen.** Eine neue Schätzung ist eine neue Wertermittlung, eine erneuerte Police ist ein neuer Versicherungseintrag. Historie funktioniert nur, wenn sie sich ansammelt.
- **Dokumente unangehängt lassen.** Eine Quittung, abgelegt bei der Transaktion, die sie belegt, ist ein Beweis. Eine lose an das Exemplar angehängte Datei ist ein Scan, den du später wieder zuordnen musst.

## Wie es weitergeht

- Jeder hier verwendete Eintragstyp hat seine eigene ausführliche Anleitung im @doc(copyHistory.index, "Abschnitt zur Exemplarhistorie").
- Sieh dir an, wie diese Einträge in die Zahlen bei @doc(insights.collectionStatistics) einfließen.
- Teilst du die Sammlung mit anderen? @doc(tutorials.inviteHousehold, "Lade deinen Haushalt oder Verein ein").
