---
id: accounts.usersAndRoles
title: Konten, Benutzer und Rollen
slug: konten-benutzer-und-rollen
section: kernkonzepte
---

# Konten, Benutzer und Rollen

KolleK ist um einen Arbeitsbereich herum gebaut, das Konto, und die Personen, die ihn teilen. Diese Seite erklärt die Grenze und das Berechtigungsmodell in einfachen Worten, sodass dich beim Zugriff nichts überrascht.

## Das Konto ist die Grenze

Ein **Konto** ist ein privater Arbeitsbereich. Jede Sammlung, jedes Objekt, jedes Exemplar, jeder Typ, jeder Tag und jeder Standort lebt innerhalb genau eines Kontos. Nichts sickert zwischen Konten, und niemand außerhalb deines Kontos kann hineinsehen, es sei denn, du @doc(sharing.overview, "teilst absichtlich eine Sammlung").

Als Emma sich registrierte, erstellte KolleK gleichzeitig zwei Dinge: ihren persönlichen Benutzer und ein frisches Konto, das ihr gehört. Lädt sie ihren Partner Sam ein, tritt er ihrem Konto bei und arbeitet im selben Katalog.

## Eine Person, ein Konto

Ein **Benutzer** ist eine authentifizierte Person, gebunden an eine E-Mail-Adresse, und ein Benutzer gehört zu genau einem Konto.

:::note
Dieselbe E-Mail-Adresse kann nicht in zwei Konten sein. Jemand, der bereits ein eigenes Konto hat, kann keine Einladung zu deinem annehmen. Wenn diese Person dir beitreten möchte, müsste sie eine andere E-Mail-Adresse verwenden oder zuerst ihr eigenes Konto löschen.
:::

## Die drei Rollen

Jedes Mitglied eines Kontos hat eine Rolle, die bei der Einladung gewählt wird und später von einem Eigentümer geändert werden kann:

- Ein **Betrachter** kann alles im Konto durchsehen, aber nichts erstellen oder ändern. Emmas Freund Leo ist Betrachter: Er kann den Katalog bewundern, ihn aber nicht bearbeiten.
- Ein **Bearbeiter** kann Katalog-Inhalte erstellen und ändern: Sammlungen, Objekte, Exemplare, Fotos und alle Verlaufseinträge. Sam ist Bearbeiter.
- Ein **Eigentümer** kann alles, was ein Bearbeiter kann, und zusätzlich das Konto selbst verwalten: Mitglieder einladen und entfernen, Rollen ändern, Kontoeinstellungen verwalten und das Konto löschen. Emma ist Eigentümerin.

Lesen steht jedem Mitglied offen, auch Betrachtern. Schreiben braucht Bearbeiter oder Eigentümer. Das Konto verwalten braucht Eigentümer. Die Seite @doc(collaboration.rolesInPractice, "Rollen in der Praxis") bildet das auf konkrete Aufgaben ab, wenn du die vollständige Tabelle willst.

Ein Konto muss immer mindestens einen Eigentümer behalten. KolleK lässt nicht zu, dass der letzte Eigentümer degradiert oder entfernt wird, sodass sich ein Konto nie selbst aussperren kann.

## Ein Flag, das keine Rolle ist

Solltest du je von einem **Instanzadministrator** hören, ist das etwas völlig anderes. Es ist ein serverweites Flag für wer auch immer die KolleK-Installation selbst betreibt. Es gewährt nichts Zusätzliches innerhalb des eigenen Kontos dieser Person und hat nichts mit Betrachter, Bearbeiter oder Eigentümer zu tun. Es wird in @doc(instanceAdmin.panel, "dem Instanzadministrationsbereich") für Betreiber behandelt.

## Wie geht es weiter

- Bring jemanden mit ins Boot mit @doc(collaboration.invitePeople).
- Ändere, was ein Mitglied darf, in @doc(collaboration.manageMembersAndRoles).
- Setze die Konzepte fort mit @doc(collections.overview).
