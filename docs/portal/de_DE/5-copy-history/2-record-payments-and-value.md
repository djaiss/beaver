---
id: copies.recordPaymentsAndValue
title: Erfasse, was du bezahlt hast und was es wert ist
slug: bezahlung-und-wert-erfassen
section: exemplar-verlauf
---

# Erfasse, was du bezahlt hast und was es wert ist

Geld und Wert sind die zwei Fragen, die Sammler am häufigsten stellen, und KolleK hält sie bewusst getrennt. Eine **Transaktion** erfasst Geld, das tatsächlich den Besitzer gewechselt hat. Eine **Bewertung** erfasst, was ein Exemplar zu einem bestimmten Zeitpunkt wert ist, unabhängig davon, ob Geld geflossen ist. Diese Seite zeigt dir, wie du beides erfasst, und erklärt die Regel, die beides sauber trennt.

Wenn du @doc(copyHistory.concept, "Der Verlauf eines Exemplars erklärt") noch nicht gelesen hast, lies es zuerst. Dort wird die Idee eingeführt, dass diese Einträge nur ergänzt werden, nicht Felder sind, die du überschreibst.

## Die Regel, die alles sauber hält

Ein Kaufpreis ist eine Transaktion, keine Bewertung.

Wenn Priya eine Omega Speedmaster von 1968 für 4.200 kauft, ist das eine **Kauf**-Transaktion. Sie erfasst, was sie an diesem Tag bezahlt hat, und das ändert sich nie. Was die Uhr *wert* ist, ist eine eigene Frage, die sich über die Zeit ändert, und jede Antwort darauf ist eine eigene Bewertung.

KolleK leitet zwei Zahlen automatisch aus diesen Einträgen ab:

- Der **geschätzte Wert** eines Exemplars ist der Betrag seiner jüngsten Bewertung. Ein Exemplar ohne Bewertungen gilt als unbewertet, nicht als null wert.
- Der **bezahlte Preis** und das **Erwerbsdatum** eines Exemplars stammen aus seiner frühesten erwerbenden Transaktion (Kauf, Tausch, erhaltenes Geschenk oder Erbschaft).

Du trägst diese Zahlen niemals direkt am Exemplar ein. Du erfasst die Geschichte, und die aktuellen Zahlen ergeben sich daraus.

## Eine Transaktion erfassen

Eine Transaktion deckt jede Geld- oder Eigentumsbewegung rund um ein Exemplar ab: kaufen, verkaufen, tauschen, eine Gebühr zahlen oder es irgendwohin verschicken.

::::steps
:::step title="Öffne den Verlauf des Exemplars"
Öffne das Objekt, wechsle zum Tab **Verlauf** und wähle das gewünschte Exemplar. Öffne dann den Abschnitt **Transaktionen**.

::screenshot{label="Verlauf-Tab mit geöffnetem Transaktionen-Abschnitt"}
:::

:::step title="Eine Transaktion hinzufügen"
Wähle, eine Transaktion hinzuzufügen, und lege ihren **Typ** fest: Kauf, Verkauf, Tausch, erhaltenes Geschenk, gegebenes Geschenk, Erbschaft, Rückerstattung, Gebühr, Steuer, Versand oder Sonstiges.
:::

:::step title="Das Geld eintragen"
Trage den **Betrag** ein, sowie optional **Steuern**, **Gebühren** und **Versandkosten**, damit die tatsächlichen Gesamtkosten erfasst werden und nicht nur der Listenpreis.
:::

:::step title="Den Kontext ergänzen"
Erfasse die **Gegenpartei** (von wem du gekauft oder an wen du verkauft hast), das **Datum** und eine **Referenz** wie eine Bestell- oder Auktionslosnummer. Speichere die Transaktion.
:::
::::

Priya erfasst ihren Speedmaster-Kauf: Typ **Kauf**, Betrag 4.200, Gebühren 120 für das Auktionshaus, Gegenpartei "Fine Time Auctions" und die Losnummer als Referenz. Dieser eine Eintrag beantwortet nun, was sie bezahlt hat, wann sie es erworben hat und woher es kommt.

:::note
Die früheste erwerbende Transaktion (Kauf, Tausch, erhaltenes Geschenk oder Erbschaft) ist es, die dem Exemplar sein Erwerbsdatum gibt. Exemplare ohne eine solche Transaktion zählen in deinen Statistiken als undatiert, also erfasse sie auch für Dinge, die du vor langer Zeit gekauft hast, mit deiner besten Schätzung des Datums.
:::

## Eine Bewertung erfassen

Eine Bewertung beantwortet "was ist das gerade wert, und wie sicher bin ich mir".

::::steps
:::step title="Öffne den Abschnitt Bewertungen"
Öffne im selben Tab **Verlauf**, mit ausgewähltem Exemplar, den Abschnitt **Bewertungen**.
:::

:::step title="Eine Bewertung hinzufügen"
Wähle einen **Bewertungstyp**: eigene Schätzung, professionelles Gutachten, Marktschätzung, Versicherungswert, Auktionsschätzung, automatische Schätzung oder Sonstiges.
:::

:::step title="Trage den Wert und deine Sicherheit ein"
Trage den **Betrag** ein, wähle eine **Sicherheitsstufe** (Niedrig, Mittel, Hoch oder Unbekannt) und erfasse, **wer die Bewertung vorgenommen hat**. Speichere sie.

::screenshot{label="Formular für eine neue Bewertung mit Typ, Betrag und Sicherheitsstufe"}
:::
::::

Zwei Jahre später sagt ein Händler Priya, dass die Speedmaster etwa 5.500 einbringen würde. Sie fügt eine neue Bewertung hinzu: **Marktschätzung**, 5.500, Sicherheit **Mittel**, bewertet vom Händler. Ihre ursprüngliche Bewertung bleibt im Verlauf erhalten, und der geschätzte Wert des Exemplars wird auf die neue Zahl aktualisiert.

:::note
Eine Neubewertung schreibt immer eine neue Bewertung. Du bearbeitest die alte nie auf eine neue Zahl, sodass du eine ehrliche Aufzeichnung davon behältst, wie sich der Wert über die Zeit entwickelt hat. Diese Historie zeichnet das Diagramm "Wertentwicklung" in deinen Statistiken.
:::

## Wo diese Zahlen auftauchen

Die hier erfassten Werte speisen den Rest von KolleK: den auf jeder Sammlung angezeigten Gesamtwert, die Diagramme zu Wertentwicklung und Erwerben in @doc(insights.collectionStatistics, "den Sammlungsstatistiken") sowie die wertvollsten Objekte. Gründliche Transaktionen und Bewertungen sind es, die diese Bildschirme vertrauenswürdig machen.

## Wie geht es weiter

- Bewahre die Unterlagen beim Eintrag auf. @doc(copies.attachDocuments), etwa den Beleg an einer Transaktion oder das Gutachten an einer Bewertung.
- Versicherst du das Exemplar für diesen Wert? @doc(copies.insure).
- Baust du die vollständige Eigentumsgeschichte auf? @doc(copies.traceProvenance).
