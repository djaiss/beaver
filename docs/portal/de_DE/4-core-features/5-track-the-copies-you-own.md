---
id: copies.track
title: Deine Exemplare verfolgen
slug: deine-exemplare-verfolgen
section: kernfunktionen
---

# Deine Exemplare verfolgen

Ein Objekt für sich ist nur eine Beschreibung. Ein **Exemplar** ist dein Nachweis für ein physisches Stück, das du tatsächlich besitzt, mit eigenem Zustand, Standort, Status und Verlauf. Diese Seite behandelt das Hinzufügen von Exemplaren und jedes Feld an einem Exemplar.

Die Idee hinter dieser Trennung wird in @doc(items.itemsVsCopies) erklärt. Exemplare hinzuzufügen erfordert die Rolle Bearbeiter oder Eigentümer.

## Ein Exemplar hinzufügen

Exemplare werden direkt im Objektformular hinzugefügt, sodass du sie während des Katalogisierens erfassen kannst.

::::steps
:::step title="Das Objekt öffnen"
Öffne das Objekt und wähle, es zu bearbeiten, dann füge ein **Exemplar** hinzu.
:::

:::step title="Den physischen Zustand erfassen"
Wähle seinen **Zustand** aus der Liste und den **Standort**, an dem es aufbewahrt wird.

::screenshot{label="Exemplarzeile, Felder für Zustand und Standort"}
:::

:::step title="Status und Details festlegen"
Lass den **Status** bei Besessen für etwas, das du hast, oder wähle einen anderen Status. Fülle alle weiteren zutreffenden Felder aus und speichere dann das Objekt.
:::
::::

Besitzt du zwei vom Gleichen? Füge dem gleichen Objekt ein zweites Exemplar hinzu, nie ein zweites Objekt. Jedes Exemplar behält seinen eigenen Zustand, Standort und Verlauf.

## Die Exemplarfelder

- **Kennung.** Eine Seriennummer, eine Slab-Nummer oder jedes Merkmal, das genau dieses Exemplar festlegt. Priya erfasst die in jede ihrer Uhren eingravierte Seriennummer.
- **@doc(conditions.overview, "Zustand").** Die Note dieses Exemplars, gewählt aus der fertigen Liste (Neu, Wie neu, Gebraucht, Abgenutzt, Beschädigt, plus alle, die dein Konto ergänzt hat).
- **@doc(locations.overview, "Standort").** Wo das Exemplar aktuell liegt. Änderst du ihn später über einen Umzug, bleibt der Verlauf erhalten; siehe @doc(copies.move, "Ein Exemplar umziehen").
- **Status.** Wo das Exemplar in seinem Lebenszyklus steht. Siehe die Liste unten.
- **Menge.** Für identische, austauschbare Exemplare, die du nicht einzeln unterscheiden musst, etwa zehn desselben ungeöffneten Booster-Packs. Ist jedes Exemplar einzeln wichtig, gib jedem stattdessen eine eigene Zeile.
- **Abgabedatum.** Wann das Exemplar deinen Besitz verlassen hat, für Status wie Verkauft oder Entsorgt.
- **Notiz.** Alles, was du dir speziell zu diesem Exemplar merken willst.
- **Geschätzter Wert.** Eine schnelle Zahl dafür, was das Exemplar wert ist. Im Hintergrund wird sie als "Eigene Schätzung" @doc(copies.recordPaymentsAndValue, "Bewertung") gespeichert, die den Wertverlauf des Exemplars öffnet, statt direkt am Exemplar zu hängen. Für alles, was dir wichtig ist, füge dort richtige datierte Bewertungen hinzu.

## Der Status-Lebenszyklus

- **Besessen.** In deinem Besitz. Die Vorgabe.
- **Bestellt.** Gekauft, aber noch nicht angekommen.
- **Verliehen.** Bei jemand anderem, aber immer noch deins. Die Obhut hat gewechselt, nicht der Besitz, das Exemplar zählt also weiterhin als gehalten. Ausleihen erfasst du am besten über @doc(loans.lendAndBorrow), das diesen Status für dich setzt.
- **Verkauft, Verschenkt.** Der Besitz ist an jemand anderen übergegangen.
- **Verloren, Gestohlen.** Ohne dein Einverständnis weg.
- **Entsorgt.** Weggeworfen oder recycelt.
- **Sonstiges.** Alles, was die Liste nicht abdeckt.

Besessen, Bestellt und Verliehen zählen als "noch gehalten". Die anderen erfassen Exemplare, die die Sammlung verlassen haben, deren Verlauf du aber behalten willst.

## Wo das Geld lebt

Dir fällt vielleicht auf, dass es kein Feld "Bezahlter Preis" am Exemplar gibt. Das ist Absicht. Was du bezahlt hast und wann du das Exemplar erworben hast, stammt aus seinen **Transaktionen**, und was es im Lauf der Zeit wert ist, stammt aus seinen **Bewertungen**. So bleibt die vollständige Geldgeschichte erhalten, statt einer einzigen überschriebenen Zahl. Beginne mit @doc(copies.recordPaymentsAndValue).

## Wie es weitergeht

- Verstehe, welche Einträge ein Exemplar tragen kann: @doc(copyHistory.concept, "Der Verlauf eines Exemplars erklärt").
- Erfasse den Kauf: @doc(copies.recordPaymentsAndValue).
- Halte seine Adresse aktuell: @doc(copies.move).
