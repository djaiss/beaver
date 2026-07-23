# Product Marketing Context

*Last updated: 2026-07-22*

## Product Overview

**One-liner:** KolleK is the open source home for everything you collect.

**What it does:** KolleK is a self-hostable collection manager for cataloguing physical collections of any kind: comics, books, vinyl, trading cards, wine, watches, games, coins, stamps, and more. It helps collectors organize collections, define custom fields, track every physical copy they own, record condition, location, purchase details, value, documents, care, provenance, and history, and keep sensitive data private and encrypted.

**Product category:** Collection management software; personal inventory software; collector cataloguing app; self-hosted inventory app.

**Product type:** Open source web application with self-hosted and cloud options.

**Business model:** MIT-licensed open source self-hosting is free. Marketing copy also presents a one-time $49 cloud option with no subscription.

## Target Audience

**Target companies:** Primarily individual collectors, households, clubs, and small teams that maintain physical collections. Also relevant for small specialty sellers, archives, hobby groups, and organizations that need a private catalogue without enterprise inventory complexity.

**Decision-makers:** The collector who owns or manages the catalogue; a technically comfortable self-hoster; a household or club owner who decides who can edit or view; a privacy-conscious buyer who does not want sensitive collection data locked inside a consumer SaaS product.

**Primary use case:** Replace spreadsheets, notes apps, memory, scattered photos, and loose receipts with one private system for understanding what is owned, where each object is, what it is worth, and what has happened to it.

**Jobs to be done:**

- Catalogue a collection in enough detail that it matches the hobby instead of forcing a generic inventory template.
- Track exact physical copies, including duplicates, variants, condition, storage location, purchase details, current value, and status.
- Preserve the story and proof around valuable objects: transactions, valuations, insurance, maintenance, loans, provenance, locations, and documents.
- Share access with trusted people while keeping permissions and accountability clear.
- Own the data through self-hosting, encryption, export, API access, and open source code.

**Use cases:**

- A comic collector tracks several copies or variants of the same issue, with condition, location, paid price, and current estimated value per copy.
- A wine collector records vintages, regions, wineries, bottle counts, storage location, and drink-by context.
- A household shares one collection workspace where some people can edit and others can only view.
- A collector records insurance, maintenance, provenance, documents, and valuation history for high-value objects.
- A technical collector self-hosts KolleK to keep ownership, storage, and backups under their own control.

## Personas

| Persona | Cares about | Challenge | Value we promise |
|---------|-------------|-----------|------------------|
| Collector / Owner | Knowing exactly what they own, where it is, what it is worth, and what happened to it | Spreadsheets and memory stop working once the collection has duplicates, values, locations, or history | A private catalogue that understands both the item and each physical copy |
| Technical self-hoster | Data ownership, open source code, deployment control, backups, and avoiding lock-in | Consumer collection apps may disappear, monetize, restrict export, or hold sensitive data remotely | MIT-licensed, self-hostable software with encrypted sensitive fields and documented deployment |
| Household / club champion | Letting others help without losing control | Shared passwords and all-or-nothing access create mistakes and accountability gaps | Owner, editor, and viewer roles with invitations and activity history |
| Privacy-conscious collector | Keeping collection value, locations, and documents private | Collection data can reveal wealth, storage locations, and ownership proof | Encrypted sensitive fields, private file serving, 2FA, magic links, and self-hosting |
| API / automation user | Building on the catalogue or integrating with other tools | A closed collection app cannot be extended or scripted | A documented token-authenticated JSON API and webhooks |

## Problems & Pain Points

**Core problem:** Collectors outgrow spreadsheets, notes apps, folders of photos, and memory. Those tools can list titles, but they do not reliably capture the real object: its copy-specific condition, location, value, acquisition, documents, care, custody, and history.

**Why alternatives fall short:**

- Spreadsheets are flexible but become fragile, ugly, hard to search, and weak at photos, files, history, and collaboration.
- Notes apps can hold detail but lack structure, reporting, permissions, and consistent fields.
- Generic inventory tools do not speak the language of specific hobbies like comics, wine, watches, cards, or vinyl.
- Consumer SaaS collection tools may create lock-in, subscriptions, export anxiety, or privacy concerns.
- Simple title-based catalogues cannot distinguish duplicates, variants, editions, graded copies, reading copies, or individually valuable objects.

**What it costs them:** Time spent searching shelves, boxes, receipts, and old notes; poor confidence in value totals; duplicate purchases; misplaced items; incomplete insurance or provenance records; risk when lending or moving objects; stress when a valuable item needs proof.

**Emotional tension:** Collectors want the collection to feel cared for and understandable, not chaotic. They worry about losing track of valuable items, losing control of private data, forgetting context, or discovering too late that the important receipt, valuation, or location note is missing.

## Competitive Landscape

**Direct:** Collector-specific catalogue apps and inventory software - often solve one category well, but may be closed, subscription-based, less self-hostable, or weaker on copy-level history and data ownership.

**Secondary:** Spreadsheets, Airtable, Notion, notes apps, photo folders - flexible and familiar, but they require the collector to design and maintain the system, and they do not naturally connect copies, history, files, values, permissions, and audit trails.

**Indirect:** Memory, shelf labels, boxes, binders, personal assistants, insurance inventories - can work for a while, but they are fragmented and become unreliable as the collection grows or more people are involved.

## Differentiation

**Key differentiators:**

- Tracks each physical copy beneath one catalogue item, instead of forcing one flat row per title.
- Connects copy history across transactions, valuations, insurance, maintenance, loans, provenance, locations, and documents.
- Supports flexible custom catalogues with ready-made collection types and user-defined fields.
- Keeps data ownership central through open source licensing, self-hosting, encrypted sensitive fields, API access, and export-oriented messaging.
- Gives small groups practical collaboration through owner, editor, and viewer roles with audit trails.
- Designed for many collection categories rather than one hobby.

**How we do it differently:** KolleK treats a collection as a private workspace, an item as a catalogue entry, and a copy as the physical object the collector actually owns. This lets the app keep the title clean while preserving detailed facts and history for each real-world object.

**Why that's better:** Collectors can answer practical questions: Which exact copy do I own? Where is it? What condition is it in? What did I pay? What is it worth now? Who changed this? What proof do I have? Where is the document?

**Why customers choose us:** They want more than a list, but do not want enterprise inventory software. They care about privacy, self-hosting, open source ownership, hobby-specific structure, copy-level detail, and avoiding recurring subscriptions.

## Objections

| Objection | Response |
|-----------|----------|
| "I can do this in a spreadsheet." | Yes, until photos, duplicates, locations, values, files, history, permissions, and consistency matter. KolleK keeps the structure without making the collector maintain the system by hand. |
| "I do not want another subscription." | Self-hosting is free forever under the MIT license, and the marketed cloud option is positioned as one payment with no subscription. |
| "My collection is too specific for a generic app." | KolleK supports custom collection types, field groups, and fields so a comic, wine bottle, watch, book, card, or record can each have the metadata that fits. |
| "I do not want sensitive data in someone else's cloud." | KolleK can be self-hosted, sensitive values are encrypted at rest, and uploaded files can remain under the deployment's storage control. |
| "I only need a simple list." | KolleK may be more than needed if a title-only checklist is enough and copy-level condition, location, value, or history do not matter. |

**Anti-persona:** Someone who only wants a lightweight checklist of titles, does not care which exact copy they own, does not track value or location, and does not want to manage or use a structured catalogue.

## Switching Dynamics

**Push:** Spreadsheet fatigue; mystery boxes; duplicate uncertainty; missing receipts; unclear value; scattered photos and notes; concern about private collection data; frustration with subscription apps or category-specific tools that do not fit.

**Pull:** A private home for the whole collection; copy-level tracking; custom fields by hobby; open source self-hosting; no subscription; encrypted data; collaboration roles; history and documentation attached to the object.

**Habit:** Existing spreadsheet templates, notes, memory, physical binders, shelf labels, or a current app that is "good enough"; reluctance to migrate data; comfort with manual systems.

**Anxiety:** Import effort; whether self-hosting will be hard; whether the app supports their specific hobby; whether data can be exported; whether photos and documents are safe; whether cloud pricing or product direction will change.

## Customer Language

**How they describe the problem:**

- "Most collectors end up juggling spreadsheets, notes apps, and their own memory."
- "I think I have two."
- "Somewhere in the house."
- "The receipt you will definitely need later."
- "The box in the attic."

**How they describe us:**

- "The open source home for everything you collect."
- "The collection manager that belongs to you."
- "One focused tool."
- "A private home for every object you collect, with the detail and history each physical copy deserves."

**Words to use:** collection, catalogue, collector, self-host, own your data, private, encrypted, copy, physical copy, condition, location, value, history, provenance, documents, custom fields, no lock-in, no subscription, open source, household, club, small team.

**Words to avoid:** enterprise inventory, asset management platform, CRM, generic database, lifestyle app, AI-first, passive collection tracker, spreadsheet replacement without proof.

**Glossary:**

| Term | Meaning |
|------|---------|
| Account | The private workspace and tenant boundary. |
| Collection | A named grouping of items, with its own description, emoji, currency, visibility, categories, and linked types. |
| Item | The catalogue entry for a thing, such as a title, bottle, watch, card, or book. |
| Copy | The specific physical object owned by the collector. Multiple copies can belong to one item. |
| Collection type | A reusable schema for a kind of item, with custom fields and groups. |
| Category | A collection-specific grouping for items. |
| Set | A group of items tracked together for completion. |
| Location | A nested physical storage place, such as room, shelf, box, or case. |
| Valuation | A dated estimate of what a copy is worth. |
| Provenance | Ownership, authenticity, custody, or story events tied to a copy. |

## Brand Voice

**Tone:** Direct, useful, privacy-conscious, collector-native, lightly playful, and honest.

**Style:** Plainspoken and specific before funny. Explain the real capability clearly, then allow a small wink when it fits. Keep trust, security, money, backups, and sensitive-object claims sober and exact.

**Personality:** Practical, opinionated, transparent, warm, meticulous.

## Proof Points

**Metrics:**

- Marketing homepage references 4.8k GitHub stars.
- No customer outcome metrics found in the repo.

**Customers:**

- No named customer logos found in the repo.
- Testimonials exist as a product area, but no public testimonials are guaranteed by the codebase.

**Testimonials:**

> "[No verified testimonial quote captured yet.]" - TBD

**Value themes:**

| Theme | Proof |
|-------|-------|
| Own your data | MIT license, self-hostable Docker deployment, open source repository, API, export-oriented messaging. |
| Understand exact physical objects | Item/copy model, per-copy condition, location, acquisition, value, status, and history. |
| Fit any hobby | Custom fields, field groups, ready-made types for comics, vinyl, coins, stamps, books, video games, watches, wine, and more. |
| Preserve object history | Transactions, valuations, insurance records, maintenance records, provenance events, loans, location history, and documents. |
| Collaborate without chaos | Invitations, owner/editor/viewer roles, write permissions, activity logs, and actor attribution. |
| Keep private data safer | Encryption at rest, private file serving, two-factor authentication, magic links, recovery codes, security alerts. |

## Goals

**Business goal:** Grow adoption of KolleK among collectors who want a private, durable, flexible collection system, with self-hosting as the trust anchor and cloud as the low-friction paid option.

**Conversion action:** Primary: register or get started. Secondary: view on GitHub, self-host from the docs, or buy the one-time cloud option.

**Current metrics:** GitHub stars are shown as 4.8k in marketing copy. Other current conversion, revenue, retention, or activation metrics were not found in the repo.
