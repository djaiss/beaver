---
id: insights.collectionStatistics
title: Deine Sammlungsstatistiken verstehen
slug: sammlungsstatistiken-verstehen
section: einblicke
---

# Deine Sammlungsstatistiken verstehen

Jede Sammlung hat einen Statistikbildschirm, der deine Dateneingaben in Antworten verwandelt: Was ist sie wert, wie ist sie gewachsen und wo sitzt der Wert. Diese Seite erklärt jede Kennzahl und, genauso wichtig, woher jede Zahl stammt, damit du dem, was du liest, vertrauen kannst.

## Woher die Zahlen kommen

Zwei Regeln treiben fast alles auf diesem Bildschirm an. Sie stammen aus @doc(copyHistory.concept, "wie die Historie eines Exemplars funktioniert"):

- **Der aktuelle Wert eines Exemplars ist seine jüngste @doc(copies.recordPaymentsAndValue, "Bewertung").** Ein nie bewertetes Exemplar zählt als unbewertet, nicht als geratener Nullwert.
- **Das Erwerbsdatum eines Exemplars stammt aus seiner frühesten erwerbenden @doc(copies.recordPaymentsAndValue, "Transaktion")**, etwa einem Kauf, Tausch, erhaltenen Geschenk oder einer Erbschaft. Ein Exemplar ohne eine solche Transaktion hat kein Erwerbsdatum und kann daher nicht in den zeitbasierten Diagrammen erscheinen. Der Bildschirm sagt dir, wie viele Exemplare undatiert sind, damit du weißt, was den Diagrammen fehlt.

Wirkt ein Diagramm leerer als sich deine Sammlung anfühlt, lädt die Statistik dich damit zu mehr Dateneingabe ein, das ist kein Fehler.

## Die Gesamtzahlen

Oben: die **Objektanzahl**, die **Exemplaranzahl**, der **geschätzte Gesamtwert** (die Summe des aktuellen Werts jedes Exemplars) und der **Durchschnittswert pro Objekt**. Du siehst außerdem, was sich zuletzt geändert hat: diesen Monat hinzugefügte Objekte und diesen Monat hinzugefügter Wert.

## Set-Vollständigkeit

Hat die Sammlung @doc(sets.trackCompletion, "Sets mit einem Zielwert"), rollt der Bildschirm sie zusammen: wie viele Stücke du gegenüber dem kombinierten Ziel besitzt, und der Vollständigkeitsprozentsatz. Nur Sets mit einem Zielwert über null zählen mit. Ein Set, das mehr als sein Ziel enthält, zählt als vollständig, nicht als übervollständig.

## Wertentwicklung

Ein Zwölf-Monats-Diagramm des kumulativen geschätzten Werts deiner Sammlung, Monat für Monat. Jedes Exemplar tritt der Linie an seinem Erwerbsdatum mit seinem aktuellen Wert bei. Alles, was vor dem Zwölf-Monats-Fenster erworben wurde, ist bereits im ersten Punkt enthalten, sodass die Linie bei deiner echten Gesamtsumme beginnt, nicht bei null.

## Erwerbe pro Monat

Wie viele Exemplare du in jedem der letzten zwölf Monate erworben hast, angetrieben von denselben Erwerbsdaten. Ein ruhiges Diagramm hier bedeutet meist fehlende Erwerbstransaktionen, nicht ein ruhiges Jahr.

## Aufschlüsselungen

- **Nach Kategorie.** Wie sich Objekte über deine @doc(categories.organizeItems, "Kategorien") verteilen. Die sechs größten Kategorien werden benannt, der Rest fließt in "Sonstige", und unkategorisierte Objekte werden als eigenes Segment gezeigt.
- **Nach Zustand.** Wie deine Exemplare bewertet sind, als Anzahl und Prozentsatz je @doc(conditions.overview, "Zustand").
- **Wert nach Standort.** Der summierte Wert der Exemplare an jedem @doc(locations.overview, "Standort"), damit du weißt, was wo liegt. Priya nutzt das, um zu sehen, wie viel Wert in ihrer Vitrine gegenüber ihrem Safe steckt. Nur Standorte mit Wert erscheinen.

## Wertvollste Objekte

Die fünf wertvollsten Objekte der Sammlung, gerankt nach dem kombinierten aktuellen Wert ihrer Exemplare, jeweils mit Zustand und Standort des wertvollsten Exemplars gezeigt.

## Wie geht es weiter

- Speise die Diagramme: @doc(copies.recordPaymentsAndValue).
- Verfolge Vollständigkeit richtig: @doc(sets.trackCompletion).
- Sieh die kontoweite Ansicht: @doc(insights.dashboard).
