---
id: loans.custody
title: Leihgaben und Verwahrung
slug: leihgaben-und-verwahrung
section: kernfunktionen
---

# Leihgaben und Verwahrung

Eine Leihgabe ist ein vorübergehender Wechsel der Verwahrung ohne jeden Wechsel des Eigentums. Wenn Sie ein Stück an einen Freund, eine Galerie oder ein Museum verleihen, gehört es weiterhin Ihnen. Wenn Sie ein Stück ausleihen, gehört es weiterhin jemand anderem. Der Bereich **Leihgaben** ist die kontoweite Übersicht über alles, was sich derzeit als Leihgabe außerhalb Ihrer Hände oder in Ihren Händen befindet.

Das Ansehen des Bereichs steht jeder Rolle offen. Das Erfassen, Zurücknehmen, Bearbeiten und Löschen einer Leihgabe erfordert die Rolle **Bearbeiter** oder **Eigentümer**.

## Die zwei Richtungen

Jede Leihgabe weist in eine von zwei Richtungen, und der Bereich zeigt jeweils eine Richtung an. Verwenden Sie den Umschalter oben, um zwischen ihnen zu wechseln.

- **Verliehen.** Ein Stück von Ihnen, das jemand anderes in Verwahrung hat. Solange eine ausgehende Leihgabe aktiv oder überfällig ist, erscheint ihr Exemplar in Ihrer Sammlung als **Verliehen**, weil es sich nicht physisch bei Ihnen befindet.
- **Ausgeliehen.** Ein Stück, das jemand anderem gehört und das Sie vorerst in Verwahrung haben. Ein ausgeliehenes Stück ändert niemals, wie Ihre eigenen Exemplare erscheinen, weil es Ihnen nie gehört hat.

## Was die Reiter zeigen

Innerhalb einer Richtung teilen die Reiter dieselben Leihgaben auf unterschiedliche Weise auf.

- **Alle Leihgaben.** Jede Leihgabe in der Richtung, mit einem Suchfeld und Filtern nach Sammlung, Status und Sortierreihenfolge.
- **Fällig und überfällig.** Drei Listen: Leihgaben, deren Fälligkeitsdatum überschritten ist, Leihgaben, die innerhalb von dreißig Tagen fällig werden, und unbefristete Leihgaben, die überhaupt kein Fälligkeitsdatum haben.
- **Risiken und Ausnahmen.** Die Leihgaben, die einen zweiten Blick brauchen: überfällig, verloren, in schlechterem Zustand zurückgegeben, ohne Fälligkeitsdatum, ohne erfassten Zustand beim Hinausgeben, oder verliehen ohne jegliche Unterlagen in der Akte.
- **Nach Partei.** Eine Karte pro Person oder Institution, damit Sie auf einen Blick alles sehen, was ein einzelner Entleiher oder Verleiher gerade hält.
- **Kautionen.** Was Sie im Rahmen offener Leihgaben halten oder Ihnen geschuldet wird, und die Leihgaben, die eine Kaution tragen.
- **Zeitleiste.** Bevorstehende Fälligkeiten, kürzlich zurückgegebene Stücke und kürzlich verliehene Stücke.

Die Statistikkacheln oben sind Verknüpfungen: jede öffnet den Reiter, der ihre Zahl beantwortet.

## Eine Leihgabe erfassen

Sie können eine Leihgabe direkt aus dem Bereich heraus starten, ohne vorher das Exemplar suchen zu müssen.

::::steps
:::step title="Den Bereich für die neue Leihgabe öffnen"
Wählen Sie **Neue Leihgabe**. Wählen Sie die Richtung und steigen Sie dann von der Sammlung über das Objekt bis zu dem genauen Exemplar herab, das den Ort wechselt.
:::

:::step title="Die Partei und die Daten benennen"
Geben Sie an, an wen das Stück geht oder von wem es kommt, das Datum, an dem es fortging, und ein Fälligkeitsdatum. Setzen Sie das Häkchen bei **unbefristet**, wenn kein Rückgabedatum vereinbart ist.
:::

:::step title="Zustand und eine etwaige Kaution erfassen"
Wählen Sie den **Zustand beim Hinausgeben**, damit eine spätere Rückgabe damit verglichen werden kann, und erfassen Sie eine **Kaution**, falls ein Betrag den Besitzer wechselte. Die Währung der Kaution übernimmt standardmäßig die der Sammlung.
:::

:::step title="Für die Provenienz markieren, wenn es zur Geschichte gehört"
Setzen Sie das Häkchen bei **in die Provenienz aufnehmen** für eine institutionelle Leihgabe oder eine Ausstellung, und ein passendes Provenienzereignis wird erzeugt. Lassen Sie es weg bei einer informellen persönlichen Leihgabe, die nur im Leihverlauf verbleibt.
:::
::::

### Eine offene Leihgabe pro Exemplar

Ein physisches Exemplar kann sich immer nur an einem Ort befinden, deshalb kann ein Exemplar höchstens eine **offene ausgehende** Leihgabe haben. Wenn Sie versuchen, ein bereits hinausgegebenes Exemplar zu verleihen, blockiert der Bereich dies und bittet Sie, zuerst die aktuelle Leihgabe zurückzunehmen. Diese Regel gilt auch in der JSON-API.

## Eine Leihgabe zurücknehmen

Das Abschließen einer Leihgabe ist ein eigener Schritt, keine Bearbeitung, damit es festhält, was eine Bearbeitung nicht festhalten würde.

::::steps
:::step title="Die Leihgabe öffnen und als zurückgegeben markieren"
Öffnen Sie die Leihgabe aus einer beliebigen Liste und wählen Sie dann **Als zurückgegeben markieren**.
:::

:::step title="Die Rückgabe erfassen"
Geben Sie das Datum ein, an dem das Stück zurückkam, sowie den **Zustand beim Zurückkommen**. Das Festlegen eines Zustands beim Zurückkommen aktualisiert den aktuellen Zustand des Exemplars und bringt das Exemplar wieder in Ihre Verwahrung.
:::
::::

Wenn der Zustand beim Zurückkommen schlechter ist als der Zustand beim Hinausgeben, wird die Leihgabe als möglicher Schaden gekennzeichnet, sowohl an der Leihgabe selbst als auch in der Risikoliste **In schlechterem Zustand zurückgegeben**.

## Ausgeben, was draußen ist

Die Schaltfläche **Ausgeben, was draußen ist** lädt eine CSV-Datei der offenen Leihgaben in der aktuellen Richtung herunter, damit Sie eine schlichte Liste dessen haben, was sich gerade in fremden Händen oder in Ihren befindet.

## Verwandtes

- Leihgaben erscheinen auch im eigenen Verlauf eines Exemplars. Siehe @doc(copies.track) für den Exemplareintrag, an dem sie hängen.
