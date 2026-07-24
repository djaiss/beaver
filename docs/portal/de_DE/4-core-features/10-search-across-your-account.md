---
id: search.overview
title: Das ganze Konto durchsuchen
slug: das-ganze-konto-durchsuchen
section: kernfunktionen
---

# Das ganze Konto durchsuchen

Emma sammelt Comics, Schallplatten und Sammelkarten. Sie hat drei Sammlungen, ein paar hundert Objekte und doppelt so viele Exemplare. Jemand fragt sie, ob sie das Spider-Man-Heft mit dem schwarzen Cover noch besitzt. Sie sollte sich nicht merken müssen, in welcher Sammlung es liegt.

Die Suche beantwortet das aus einem einzigen Feld. Tippen Sie ein Wort, und KolleK schaut auf einmal in alles, was Ihr Konto enthält: Objekte, Sammlungen, Exemplare, Fotos, Leihgaben, Standorte, Sets, Reihen, Kategorien, Schlagwörter und Dokumente.

Öffnen Sie sie über **Suche** oben in der Seitenleiste, oder drücken Sie **⌘K** (**Strg K** unter Windows und Linux) von jedem Bildschirm aus.

## Was durchsucht wird

Die Suche schaut nicht nur auf Namen. Sie schaut auch auf die Wörter, die um einen Datensatz herum abgelegt sind, und genau das macht sie nützlich statt bloß wörtlich.

Die Suche nach `spider` findet:

- ein Objekt namens **Amazing Spider-Man #300**
- das Exemplar **ASM-300-B**, weil sein Objekt so heißt
- ein Foto namens `spider-man-300-front.jpg`
- ein Objekt, das Sie nie „Spider-Man“ genannt haben, weil Sie ihm das Schlagwort **spider-man** gegeben haben
- eine Leihgabe an einen Freund, weil das verliehene Exemplar dieser Comic ist

Eine Suche, die von einem Schlagwort, einer Kategorie oder einem Standort ausgeht, bringt Sie also trotzdem zu dem, was Sie eigentlich gesucht haben.

:::note
Alles im Papierkorb bleibt aus der Suche heraus. @doc(dataSafety.restoreFromTrash, "Stellen Sie es wieder her"), und es ist wieder auffindbar.
:::

## Wie die Treffer zustande kommen

Vier Regeln decken alles ab.

**Jedes Wort muss passen.** Ein zusätzliches Wort verengt das Ergebnis, statt es zu erweitern. `miles davis` findet nur die Datensätze, in denen beide Wörter irgendwo vorkommen. Wenn zu viel zurückkommt, fügen Sie ein Wort hinzu.

**Ein Wort passt von seinem Anfang her.** `spi` findet **Spider-Man**. Sie müssen ein Wort nie zu Ende tippen, aber Sie müssen es beginnen: Die Suche nach `man` findet **Spider-Man** nicht, weil `man` der Anfang keines seiner Wörter ist.

**Groß- und Kleinschreibung sowie Satzzeichen werden ignoriert.** `asm-300`, `asm 300` und `ASM_300` verhalten sich gleich, was zählt, wenn Ihre eigenen Kennungen Bindestriche, Punkte oder Unterstriche verwenden und Sie nicht mehr wissen, welchen.

**Einzelne Buchstaben werden ignoriert.** Ein Buchstabe für sich ist zu häufig, um indexiert zu werden, und fällt deshalb aus Ihrer Suche heraus. Wenn Sie nur einen einzelnen Buchstaben suchen, bekommen Sie nichts statt alles.

## Die Ergebnisse lesen

Die Ergebnisse sind danach gruppiert, was sie sind, mit den Objekten zuerst. Jede Zeile zeigt den Namen, ein Abzeichen für die Art des Datensatzes, die Sammlung, zu der er gehört, sofern es eine gibt, und eine Kontextzeile: wie viele Exemplare ein Objekt hat, wo ein Exemplar liegt, an wen eine Leihgabe ging.

Rechts in jeder Zeile heißt **Treffer im Namen**, dass jedes getippte Wort im Namen des Datensatzes selbst vorkam. **Treffer im Text** heißt, dass mindestens ein Wort weiter außen gefunden wurde, etwa in einer Beschreibung oder einem Schlagwort. Namenstreffer stehen zuerst, die nächstliegende Antwort ist also meist die erste Zeile.

Mit den Chips über den Ergebnissen schränken Sie auf eine Art von Datensatz ein. Jeder hat seine eigene Adresse, eine Suche nur nach Objekten lässt sich also als Lesezeichen speichern oder mit einer Kollegin im selben Konto teilen.

Es werden höchstens 50 Ergebnisse gezeigt. Gibt es mehr, nennt die Zahl unter der Liste, wie viele insgesamt gepasst haben, und ein weiteres Wort in der Suche ist der schnellste Weg zu dem einen, das Sie meinen.

## Wer was durchsuchen darf

Die Suche bleibt in Ihrem Konto. Sie sehen nie etwas aus einem anderen Konto, und niemand außerhalb sieht Ihres.

Innerhalb Ihres Kontos darf jede Rolle suchen, und jedes Ergebnis öffnet einen Bildschirm, den diese Rolle öffnen darf. @doc(accounts.usersAndRoles, "Leser") sind die eine Ausnahme: Schlagwörter werden auf einem Bildschirm verwaltet, den nur Eigentümer und Bearbeiter haben, ein Leser bekommt also keine eigenständigen Schlagwort-Ergebnisse. Er findet trotzdem alles, was mit `spider-man` *ausgezeichnet* ist, weil Schlagwortnamen für die Objekte zählen, die sie tragen.

## Wenn etwas fehlt

Die Suche liest einen Index, der beim Arbeiten mitgeführt wird, ein neues Objekt ist also in dem Moment auffindbar, in dem Sie es speichern.

Zwei Fälle können ihn kurz hinterherhinken lassen:

**Sie haben etwas umbenannt, das andere Datensätze nennen.** Eine umbenannte Sammlung heißt, dass jedes ihrer Objekte unter dem neuen Namen neu indexiert werden muss. Das geschieht im Hintergrund, geben Sie ihm einen Moment.

**Sie haben gerade eine selbst gehostete Instanz aktualisiert.** Der Index startet auf einer bestehenden Installation leer und muss einmal aufgebaut werden. Führen Sie @doc(selfHosting.cliCommands, "den Neuaufbau-Befehl") aus, und alles wird auffindbar:

```bash
php artisan search:rebuild-index
```

Der Befehl lässt sich jederzeit gefahrlos erneut ausführen und ist damit auch die Abhilfe, falls der Index je abdriftet.

## Wie es weitergeht

- @doc(items.tagAndFind) behandelt das Verschlagworten, das die Suche Dinge finden lässt, die Sie nicht wörtlich benannt haben.
- @doc(organizing.categoriesSetsAndSeries) erklärt die Kategorien, Sets und Reihen, die ebenfalls in die Suche einfließen.
- @doc(photos.library) hat eine eigene Suche, für den Fall, dass Sie Fotos durchgehen statt das ganze Konto.
