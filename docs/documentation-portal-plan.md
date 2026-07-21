# Documentation Portal Plan

A roadmap for the KolleK product documentation portal.

This document describes what the documentation portal should contain, section by section and page by page. It is an implementation plan, not the documentation itself. A separate writing agent should be able to build every page from this roadmap without having to rethink the structure.

---

## How to use this roadmap

Read this section first. It sets the scope, the naming, and the conventions used throughout.

### What the product is

KolleK is a self hostable web application for cataloguing collections of any kind (comics, vinyl, coins, watches, wine, books, cards, video games, and so on). Every account is a private, multi user workspace. Users create collections, describe what those collections hold with their own custom fields, add items with photos and tags, and track each physical copy they own, including its condition, storage location, value, and full history. Sensitive data is encrypted at rest. A token authenticated JSON API mirrors the whole application.

The product is branded **KolleK** in user facing surfaces. The codebase and command namespace are internally called **beaver**. Documentation should always say KolleK to the reader, and only mention "beaver" where a user literally types it (for example the `beaver:make-instance-administrator` command).

### Who the readers are

Three distinct audiences, each of which needs different pages:

1. **Collectors** who use the app. Not developers. They want to catalogue their things and find them later. This is the primary audience and most of the portal serves them.
2. **Account owners and administrators** who invite others, manage roles, and look after account settings.
3. **Operators and developers** who self host an instance or build against the API.

Every page below names its audience. Never assume a collector understands developer or operator vocabulary.

### Scope boundaries (read carefully)

- **The API reference already exists and is generated from the code.** It is served at `/docs/api` (with Markdown mirrors at `/docs/api.md` and `/docs/api/{section}.md`), built by the `ApiDocumentation` service from the definition files in `resources/docs/api`. The product portal must **not** duplicate the endpoint reference. It should link to it, and add the narrative that generated reference cannot: authentication walkthroughs, concepts, and worked examples.
- **There is no billing, subscription, or payment system in the application.** Do not write pricing, plan, trial, or upgrade documentation as if money changes hands inside the app. Self hosting is free. Managed hosting, if offered, is a one time fee handled entirely outside the product. The `trial_ends_at` field exists in the database but nothing reads it. The marketing pricing page is deliberate satire and is not a product surface to document.
- **A public product documentation portal does not exist yet.** Only `/docs/api` is routed today. The bare `/docs` path, or `/help` or `/guide`, is a natural home for this portal.

### Marking maturity

Some capabilities are built but not yet wired up, or are visible in the UI as placeholders. Every page that touches one of these must clearly separate current behavior from planned behavior. The known cases, flagged at the point they appear below, are:

- **Global search** on the web is a placeholder screen labelled "coming soon." (Photo search and in collection filtering do work; see those pages.)
- **Webhooks** can be registered and will receive a correctly signed payload shape, but no application event fires one yet. Document webhooks as "set up now, delivery arriving as the domain grows," not as a working automation feature.
- **Custom conditions** have no web management screen. Conditions appear as dropdowns everywhere, and the seeded defaults work, but creating or renaming a condition is currently only possible through the API.
- **Import and export** exist only for collection types (as JSON schema). There is no item or whole collection import or export yet.
- **Instance admin Support and Reviews** pages are stubs that say they are not built.

Never present a planned or stubbed capability as finished.

### Conventions for every page entry

Each proposed page below lists:

- **Purpose.** What the page accomplishes for the reader.
- **Audience.** Which of the three reader groups it serves.
- **Summary.** What the page should cover, grounded in actual behavior.
- **Prerequisites.** Pages a reader should have read, or state they should have reached, first.
- **Related pages.** Where to go next or sideways.

Sections are ordered by how a new user naturally learns the product. A beginner should never need an advanced concept to finish a basic task.

---

## Section 1: Getting Started

**Why this section exists.** A brand new user needs the shortest possible path from "I just found this" to "I catalogued my first thing." This section is the front door. It answers "what is this, is it for me, and how do I begin," and it deliberately defers every advanced idea.

**Who it is for.** Collectors evaluating or opening the app for the first time.

### What is KolleK

- **Purpose.** Tell a newcomer in one page what the product does and whether it fits them.
- **Audience.** Collectors (and evaluators).
- **Summary.** The problem it replaces (spreadsheets, notes apps, memory). The one line mental model: an account holds collections, collections hold items, items have physical copies. Who it suits (a single collector or a shared group). The two facts that make it different: it is private and encrypted, and it is yours to self host. Set expectations that it is open source and free to run yourself.
- **Prerequisites.** None. This is the true entry point.
- **Related pages.** Create your account; Core concepts overview.

### Cloud version versus self hosting

- **Purpose.** Help a reader choose how they will run KolleK before they invest time.
- **Audience.** Collectors and prospective operators.
- **Summary.** The two ways to run it: self host it yourself for free (points to the Self Hosting section), or use a managed instance if one is offered. Be explicit that there is no in app subscription and no paywalled features: the same application is the same application either way. Do not invent hosting tiers.
- **Prerequisites.** What is KolleK.
- **Related pages.** Self Hosting overview; Create your account.

### Create your account

- **Purpose.** Get a new user signed up and logged in.
- **Audience.** Collectors.
- **Summary.** The registration form (first name, last name, email, password). Note the guardrails so users are not surprised: disposable email addresses are rejected, and passwords are checked against known breaches and must be at least eight characters. Explain that signing up creates both a personal user and a new account that the user owns. Mention that a fresh account arrives pre seeded with a dozen ready made collection types and some default locations, so the app is not empty. Note email verification exists but is not currently required to use the app.
- **Prerequisites.** What is KolleK.
- **Related pages.** Signing in; The getting started checklist; Understanding accounts and roles.

### Signing in

- **Purpose.** Cover every way to get back into the app.
- **Audience.** Collectors.
- **Summary.** Three sign in paths: email and password, a passwordless magic link (valid for five minutes, sent to the account email), and, if enabled, a two factor code step after the password. Explain that magic link sign in skips the two factor step by design. Where to go if a password is forgotten. Keep security setup itself in the Security section and link to it.
- **Prerequisites.** Create your account.
- **Related pages.** Reset your password; Protect your account with two factor authentication; Magic links explained.

### The getting started checklist

- **Purpose.** Orient a new user around the five things worth doing first, using the app's own onboarding screen.
- **Audience.** Collectors, especially the account owner.
- **Summary.** Walk through the in app checklist that appears on first use: configure collection types, set up tags, add other members, add locations, and create the first collection. Explain that the checklist ticks itself off from real data (there is no manual "mark done"), that it is account wide rather than per user, and that only an owner can hide it, from account settings. Frame it as a suggested order, not a requirement.
- **Prerequisites.** Create your account.
- **Related pages.** Create your first collection; Organize where things live (locations); Invite people to your account.

### A five minute quick start

- **Purpose.** A single, fast, end to end path from empty account to one catalogued item with a photo and a copy.
- **Audience.** Collectors who want to feel success immediately.
- **Summary.** The condensed happy path: create a collection, add one item, upload a photo, add one copy with a condition and location, and view it. Deliberately thin, linking out to the fuller how to guides for anything a reader wants to go deeper on. This is the "does this work for me" page.
- **Prerequisites.** Create your account.
- **Related pages.** Create your first collection (tutorial); Add and edit items; Track the copies you own.

---

## Section 2: Core Concepts

**Why this section exists.** KolleK has a specific and slightly unusual data model. The single biggest source of confusion is the difference between an item and a copy. If a reader understands the concepts here, every task page later becomes obvious. These are explanatory pages, not task pages, and they should lean on concrete collector examples over definitions.

**Who it is for.** Every collector, ideally read once early, then referenced later.

### How KolleK is organized (the big picture)

- **Purpose.** Give the reader the whole mental model on one page before any detail.
- **Audience.** Collectors.
- **Summary.** The spine: account, then collections, then items, then copies. Where the shared, account wide helpers sit (types and custom fields, series, tags, locations, conditions). A simple diagram or nested list. End by pointing at the concept pages that zoom into each layer. This page is the map; the rest of the section is the territory.
- **Prerequisites.** What is KolleK.
- **Related pages.** Every other page in this section.

### Accounts, users, and roles

- **Purpose.** Explain the tenancy and permission model in plain language.
- **Audience.** Collectors and owners.
- **Summary.** An account is the workspace and the boundary: everything lives inside exactly one account. Users belong to one account (a person cannot be in two accounts with the same email). The three roles and what each can do: viewers read, editors create and change catalogue content, owners also manage the account, members, types, tags, and settings. Note in passing that a separate instance administrator flag exists for whoever runs the server and is unrelated to these roles (full detail lives in the Administration section).
- **Prerequisites.** How KolleK is organized.
- **Related pages.** Invite people to your account; Manage members and roles; Instance administration.

### Collections

- **Purpose.** Explain what a collection is and the choices a reader makes when creating one.
- **Audience.** Collectors.
- **Summary.** A collection is a top level grouping the user names, for example "My Comics" or "Wine Cellar." Each carries an emoji, a description, its own currency (which can differ from the account default), and a visibility. Introduce visibility conceptually here (private, shared with the account, or public by link, read only) and link to the sharing how to. Note that a collection can enable one or more collection types, which is what decides the custom fields its items can use.
- **Prerequisites.** How KolleK is organized.
- **Related pages.** Items and copies; Collection types and custom fields; Share a collection.

### Items versus copies (the key distinction)

- **Purpose.** Remove the single most important source of confusion in the whole product.
- **Audience.** Collectors. Essential reading.
- **Summary.** An item is the kind of thing ("Amazing Spider-Man #1"). A copy is one physical instance you actually own. Own three, and that is one item with three copies, each with its own condition, storage location, value, and history. Use a worked example. Make the rule explicit and repeat it: descriptive and classification details live on the item; everything about condition, location, money, and history lives on the copy. This page is referenced by nearly every task page, so it must be crisp.
- **Prerequisites.** Collections.
- **Related pages.** Track the copies you own; A copy's history explained; Record what you paid and what it is worth.

### Collection types and custom fields

- **Purpose.** Explain the user defined schema that tailors what each item records.
- **Audience.** Collectors, especially whoever sets up the account.
- **Summary.** A collection type (Comics, Vinyl, Wine) decides which custom fields apply to an item. Types are account wide and reusable, and a collection can enable several. Custom fields are defined on the type, and their values are recorded on each item. Field groups are named sections that keep long forms readable. The available field types: text, number, date, yes/no, select, and rating (up to five stars). Note the ready made types the app ships with. Keep the how to (creating fields, reordering, import and export) in its own guide and link to it.
- **Prerequisites.** Collections; Items versus copies.
- **Related pages.** Set up collection types and custom fields (how to); Import and export a collection type.

### Categories, sets, and series

- **Purpose.** Disentangle three grouping tools that readers routinely confuse.
- **Audience.** Collectors.
- **Summary.** Draw the distinctions clearly with examples. A **category** files items within one collection and can nest (Marvel inside Comics, Spider-Man inside Marvel). A **set** is a finite list you are trying to complete within one collection, and it can track completion against a target count. A **series** is a broad franchise that can span multiple collections (Harry Potter across books, films, and LEGO) and does not track completion. Each answers a different question: where does this file, which finite list is this part of, and which franchise is this.
- **Prerequisites.** Collections; Items versus copies.
- **Related pages.** Organize items with categories; Track a set to completion; Group a franchise with series.

### Tags

- **Purpose.** Explain the lightest weight way to slice across items.
- **Audience.** Collectors.
- **Summary.** Tags are free form, reusable labels shared across every collection in the account ("Signed," "First Issue"). An item can carry many. Contrast with categories and sets: tags cut across collections and impose no hierarchy. Deleting a tag removes the label but never the items.
- **Prerequisites.** Items versus copies.
- **Related pages.** Tag and find items; Categories, sets, and series.

### Locations

- **Purpose.** Explain how physical storage is modelled.
- **Audience.** Collectors.
- **Summary.** A location is where a copy physically lives, and locations nest as deeply as needed (a shelf inside a box inside a room). They are account wide and reused everywhere. Each copy points at its current location, and moving a copy is recorded over time (link to the copy history concept). Locations can carry an emoji.
- **Prerequisites.** Items versus copies.
- **Related pages.** Set up your locations; Move a copy and keep its location history.

### Conditions

- **Purpose.** Explain how the state of a copy is graded.
- **Audience.** Collectors.
- **Summary.** A condition grades a copy (New, Very Good, Damaged, and so on). The app ships with shared default conditions, and an account can, in principle, add its own. Flag clearly that adding or renaming conditions currently has no dedicated web screen and is only available through the API; in the web app, conditions appear as ready made dropdown choices when editing a copy, recording maintenance (before and after), or logging a loan (out and in).
- **Prerequisites.** Items versus copies.
- **Related pages.** Track the copies you own; Record maintenance and repairs.

### A copy's history explained

- **Purpose.** Explain the conceptual heart of the product: a copy stores its current state, while its history lives in separate, dated records.
- **Audience.** Collectors who want to track value, movement, and provenance.
- **Summary.** Introduce the idea that a copy carries "current state" pointers (its condition, its current location, its latest estimated value) while everything historical is an append only record hanging off the copy. Name the record types at a high level and say what each is for: transactions (money), valuations (worth over time), insurance, loans, maintenance, provenance, and location history. State the two rules that make the model coherent: money only ever lives in transactions, and revaluing or re insuring writes a new record rather than overwriting the old one. Each record type gets its own how to page; this page is the orientation.
- **Prerequisites.** Items versus copies.
- **Related pages.** Record what you paid and what it is worth; Insure a copy; Lend and borrow copies; Record maintenance; Trace a copy's provenance; Attach documents.

### Visibility and sharing

- **Purpose.** Explain the privacy model of a collection before a reader shares anything.
- **Audience.** Collectors and owners.
- **Summary.** The three visibility levels and exactly who can see each: private (only you), shared (everyone in the account), and public (anyone with the link, read only). Explain the consequence plainly: making a collection public means anyone with the link can view it without signing in. Where the setting lives (on the collection). Keep the step by step in the how to.
- **Prerequisites.** Collections; Accounts, users, and roles.
- **Related pages.** Share a collection; Manage members and roles.

### How your data is protected

- **Purpose.** Reassure a privacy conscious reader and set accurate expectations.
- **Audience.** Collectors, owners, and operators.
- **Summary.** Explain in user terms that sensitive fields are encrypted at rest in the database using the instance's key, that every change is written to an audit trail, and that the app records who did what. Be honest about the boundary: encryption at rest protects the database contents, it is not end to end encryption, and whoever runs the instance holds the key. Point operators to the deeper key management and backup guidance in Self Hosting.
- **Prerequisites.** How KolleK is organized.
- **Related pages.** The activity feed and audit trail; Security overview; Back up and restore your instance.

---

## Section 3: Core Features (how to guides)

**Why this section exists.** Once a reader understands the concepts, they need task focused pages that walk through doing the thing. These are goal titled ("Create your first collection"), not feature titled ("Collection model"). Each page assumes the relevant concept page has been read and links back to it rather than re explaining it.

**Who it is for.** Collectors and editors doing day to day work.

### Create and manage collections

- **Purpose.** Create, edit, and delete collections.
- **Audience.** Collectors with editor or owner role.
- **Summary.** Creating a collection and every field on the form (name, description, emoji from the fixed palette, visibility, currency, and which collection types apply). Editing and deleting. Explain that deleting a collection is a soft delete that sends it to the trash, and warn that it takes its items with it. Note that viewers can browse but not create or change collections.
- **Prerequisites.** Collections (concept); Accounts, users, and roles.
- **Related pages.** Add and edit items; Share a collection; Restore something from the trash.

### Choose how to view a collection

- **Purpose.** Explain the grid, list, and table layouts and in collection search.
- **Audience.** Collectors.
- **Summary.** The three view modes and how to switch, noting the choice is remembered per user, per collection. How the item count and total value are shown. Be precise about search behavior: filtering and searching happen within the currently loaded page of items, so it is a fast in view filter, not a global search. Cross reference that global search across everything is a separate, not yet available feature.
- **Prerequisites.** Create and manage collections.
- **Related pages.** Add and edit items; The search feature (status page).

### Add and edit items

- **Purpose.** The central task page: put things into the catalogue.
- **Audience.** Collectors with editor or owner role.
- **Summary.** The full item form: name, description, type, category, set, series, tags (pick existing or type new ones), custom field values (driven by the chosen type), photos, and one or more copies created inline. Explain that the same form is used to add and to edit. Note deletion is a soft delete to the trash. Point at the dedicated pages for photos and for copy details rather than covering them fully here.
- **Prerequisites.** Items versus copies; Collection types and custom fields.
- **Related pages.** Add photos to an item; Track the copies you own; Tag and find items.

### Track the copies you own

- **Purpose.** Record and manage the physical copies of an item.
- **Audience.** Collectors with editor or owner role.
- **Summary.** Adding one or more copies to an item and every copy field: identifier (a serial or slab number), condition, current location, status, quantity, disposed date, note, and estimated value. Explain the status lifecycle values (owned, ordered, loaned, sold, gifted, lost, stolen, disposed, other) and what "still held" means (a loaned copy is still held because custody moved, not ownership). Set up the idea that a copy's price paid, acquisition date, and value are not typed here directly but come from its transactions and valuations, and link to those pages.
- **Prerequisites.** Items versus copies; Conditions; Locations.
- **Related pages.** A copy's history explained; Record what you paid and what it is worth; Move a copy.

### Add photos to an item

- **Purpose.** Upload, order, and choose the main photo for an item.
- **Audience.** Collectors with editor or owner role.
- **Summary.** Uploading images (with the size limit), reordering them, and designating one as the main cover. Explain that photos attach to the item, not to a single copy. Mention that the account wide photo library (a separate page) is where all images can be searched and managed in bulk.
- **Prerequisites.** Add and edit items.
- **Related pages.** Browse and manage your photo library; Attach documents to a copy.

### Tag and find items

- **Purpose.** Apply tags and use them to slice across the catalogue.
- **Audience.** Collectors with editor or owner role.
- **Summary.** Adding a tag to an item (choosing an existing one or creating a new one on the spot) and removing tags. Managing the account tag list from settings. Realistic examples of good tagging habits. Note the consequence that deleting a tag from the account removes it from every item.
- **Prerequisites.** Tags (concept).
- **Related pages.** Organize items with categories; Categories, sets, and series.

### The activity feed and audit trail

- **Purpose.** Explain where "who changed what" surfaces.
- **Audience.** Collectors and owners.
- **Summary.** Two views of the same history: the dashboard activity feed (recent actions across the whole account) and an item's own activity log (everything that happened to that item, including before and after change details). Explain that the record survives even after a user is deleted, because the actor's name is captured at the time. Where each is found.
- **Prerequisites.** Accounts, users, and roles.
- **Related pages.** How your data is protected; Your personal activity log.

---

## Section 4: Tracking a Copy's History

**Why this section exists.** The history records on a copy are the product's deepest and most differentiated feature, and they deserve their own focused section rather than being buried in item editing. Each record type is a distinct task with its own fields and its own reason to exist. All of these pages share one prerequisite (the copy history concept) and one location (the item's History tab, one copy at a time).

**Who it is for.** Collectors who track value, provenance, insurance, loans, care, and movement. Editors and owners only, since these are write actions.

### Record what you paid and what it is worth

- **Purpose.** Capture the money and the value of a copy correctly.
- **Audience.** Collectors with editor or owner role.
- **Summary.** Two related but separate records. A **transaction** records money and ownership changes (purchase, sale, trade, gift given or received, inheritance, refund, fee, tax, shipping, other), with amount, taxes, fees, shipping, counterparty, date, and reference. A **valuation** records what a copy is worth at a point in time, with a valuation type, an amount, a confidence level, and who valued it. Make the crucial rule explicit: a purchase price is a transaction, not a valuation, and a copy's "estimated value" always comes from its most recent valuation while its "price paid" and acquisition date come from its earliest acquiring transaction. Explain that revaluing writes a new valuation rather than editing the old one, so the history is preserved.
- **Prerequisites.** A copy's history explained; Track the copies you own.
- **Related pages.** Trace a copy's provenance; Attach documents to a copy; Insights and statistics.

### Insure a copy

- **Purpose.** Record insurance coverage for a copy.
- **Audience.** Collectors with editor or owner role.
- **Summary.** Creating an insurance record: provider, insured value, policy number, coverage type, deductible, start and end dates, whether it is a scheduled item, insurer contact details, and status (active, expired, cancelled, pending). Explain that a copy can carry many insurance records over time and that expired or cancelled ones read as dimmed history behind the current one.
- **Prerequisites.** A copy's history explained.
- **Related pages.** Record what you paid and what it is worth; Attach documents to a copy.

### Lend and borrow copies

- **Purpose.** Track custody when a copy leaves your hands or arrives from someone else.
- **Audience.** Collectors with editor or owner role.
- **Summary.** Creating a loan with a direction (outgoing, lent to a friend or gallery, or incoming, borrowed in), a status (planned, active, overdue, returned, cancelled, lost), the party, purpose, dates, a deposit, and the condition of the copy when it left. Explain that an outstanding outgoing loan makes the copy read as "loaned out," and that returning a loan is a separate step that captures the return date and the condition it came back in, so transit damage is visible. Note that a scheduled job automatically flags active loans as overdue once their due date passes.
- **Prerequisites.** A copy's history explained; Conditions.
- **Related pages.** Move a copy and keep its location history; Overdue loan handling (how overdue is flagged).

### Record maintenance and repairs

- **Purpose.** Log care, repair, and conservation work on a copy.
- **Audience.** Collectors with editor or owner role.
- **Summary.** Creating a maintenance record: type (cleaning, repair, servicing, conservation, restoration, replacement, inspection), title, description, who performed it, when, cost, the condition before and after, and an optional next due date for recurring care. Explain that significant work can optionally be included in the copy's provenance narrative, and that a next due date lets the app surface upcoming care.
- **Prerequisites.** A copy's history explained; Conditions.
- **Related pages.** Trace a copy's provenance; Attach documents to a copy.

### Trace a copy's provenance

- **Purpose.** Build the ownership and authenticity story of a copy.
- **Audience.** Collectors of valuable or historically significant items.
- **Summary.** Creating provenance events: the event types (acquisition, sale, gift, inheritance, ownership or custody transfer, loan, return, exhibition, authentication, appraisal, significant restoration, origin, discovery, other), a title, parties, location, reference, a verified flag with a note, and a date. Explain the date precision idea, because provenance dates are often uncertain: an event can be dated exactly, to a month, to a year, approximately, or left unknown, and it displays accordingly. Make the money rule explicit again: provenance events carry no amounts; an event tied to a purchase or sale links to its transaction instead. Note that provenance reads oldest first as a narrative.
- **Prerequisites.** A copy's history explained; Record what you paid and what it is worth.
- **Related pages.** Record maintenance and repairs; Attach documents to a copy.

### Move a copy and keep its location history

- **Purpose.** Change where a copy lives while preserving where it has been.
- **Audience.** Collectors with editor or owner role.
- **Summary.** Recording a move: choosing a new location, the date, a reason, and a note. Explain that a move closes the previous location record and opens a new one, that the copy's current location always reflects the open record, and that correcting a past record is possible. Contrast this with simply picking a location when first creating a copy.
- **Prerequisites.** Locations; Track the copies you own.
- **Related pages.** Set up your locations; A copy's history explained.

### Attach documents to a copy

- **Purpose.** Keep receipts, appraisals, certificates, and other paperwork with the copy.
- **Audience.** Collectors with editor or owner role.
- **Summary.** Attaching either an uploaded file or an external link, to the copy itself or to any one of its records (a receipt to a transaction, an appraisal to a valuation, a policy to an insurance record, a certificate to a provenance event, and so on). The document types available, the accepted file kinds and size limit, and the metadata fields (name, description, issued date, reference). Explain that stored files are served privately and scoped to the account.
- **Prerequisites.** A copy's history explained.
- **Related pages.** Record what you paid and what it is worth; Insure a copy; Trace a copy's provenance.

### Read the copy timeline

- **Purpose.** Explain the combined timeline view that pulls every record together.
- **Audience.** Collectors.
- **Summary.** How the History tab presents one copy at a time, with a default timeline that merges transactions, valuations, insurance, loans, maintenance, provenance, location moves, and documents into a single chronological story, plus the per record sections for focused work. This is a reading and navigation page, not a data entry page.
- **Prerequisites.** A copy's history explained.
- **Related pages.** Every page in this section.

---

## Section 5: Organizing Your Catalogue

**Why this section exists.** Beyond adding items, users need to shape the structure that makes a large catalogue navigable: the types and fields, the groupings, the storage map, and the labels. These are mostly owner and editor setup tasks, often done once and refined over time. Several were introduced as concepts in Section 2; here they become step by step guides.

**Who it is for.** Owners and editors setting up and tidying an account.

### Set up collection types and custom fields

- **Purpose.** Define what each kind of item records.
- **Audience.** Owners and editors.
- **Summary.** Creating a type and setting its name and color. Adding custom fields and choosing a field type (text, number, date, yes/no, select with options, rating). Organizing fields into named groups so long forms stay readable, and reordering both fields and groups. Explain that changes save as you go in the inline editor, and that a type can be attached to any number of collections. Reference the ready made types that ship with a new account.
- **Prerequisites.** Collection types and custom fields (concept).
- **Related pages.** Import and export a collection type; Create and manage collections.

### Import and export a collection type

- **Purpose.** Move a type definition in or out as a JSON schema.
- **Audience.** Owners and editors, and power users sharing setups.
- **Summary.** Exporting a type to a downloadable JSON file (its name, color, groups, fields, and options), and importing a type by pasting JSON. A worked example using the sample Comics schema. Be clear about the boundary: this moves the type definition only, not items or their data, and there is currently no item or collection level import or export.
- **Prerequisites.** Set up collection types and custom fields.
- **Related pages.** Set up collection types and custom fields.

### Organize items with categories

- **Purpose.** Build and use a nested category tree within a collection.
- **Audience.** Owners and editors.
- **Summary.** Creating categories, nesting them under a parent, and viewing a collection filtered to one category with its own item counts and statistics panel. Explain that a category belongs to one collection, that deleting a category keeps its items (they just lose that filing), and that categories are a soft delete to the trash.
- **Prerequisites.** Categories, sets, and series (concept).
- **Related pages.** Track a set to completion; Group a franchise with series.

### Track a set to completion

- **Purpose.** Use sets to know what is owned versus still needed.
- **Audience.** Collectors, owners, and editors.
- **Summary.** Creating a set and giving it a target count so completion can be tracked, then seeing owned versus target roll up. Explain that a set belongs to one collection, and that only a set with a target above zero contributes to completion statistics.
- **Prerequisites.** Categories, sets, and series (concept).
- **Related pages.** Organize items with categories; Insights and statistics.

### Group a franchise with series

- **Purpose.** Tie together items that belong to one franchise across collections.
- **Audience.** Collectors, owners, and editors.
- **Summary.** Creating a series and linking items to it from the item form, then viewing a series with its items grouped by collection and a count of how many collections it reaches. Reinforce the contrast with sets: a series spans collections and does not track completion.
- **Prerequisites.** Categories, sets, and series (concept).
- **Related pages.** Track a set to completion; Add and edit items.

### Set up your locations

- **Purpose.** Build the physical storage map.
- **Audience.** Owners and editors.
- **Summary.** Creating locations, nesting them under a parent to model real spaces (room, then shelf, then box), and giving them an emoji. Explain that locations are account wide and reused as a copy's current location and throughout move history.
- **Prerequisites.** Locations (concept).
- **Related pages.** Move a copy and keep its location history; Track the copies you own.

### Manage account tags

- **Purpose.** Curate the shared tag vocabulary.
- **Audience.** Owners and editors.
- **Summary.** Creating, renaming, and deleting tags from account settings, as opposed to creating them ad hoc on an item. The consequence of deleting a tag (removed from every item, items unaffected otherwise). Good practices for keeping the tag list clean.
- **Prerequisites.** Tags (concept); Tag and find items.
- **Related pages.** Tag and find items.

### Browse and manage your photo library

- **Purpose.** Work with every image in the account in one place.
- **Audience.** Owners and editors.
- **Summary.** The photo library screen: searching across photos (including by the item they belong to), filtering by covers versus extras, sorting by date or size, and the storage and count statistics at the top. Per photo details (dimensions, size, format, uploader, linked item), setting a photo as its item's cover, deleting one photo, and bulk deleting a selection. Note the grid and list layout preference is remembered per user. This is the one screen with true bulk actions, so call that out.
- **Prerequisites.** Add photos to an item.
- **Related pages.** Add photos to an item.

---

## Section 6: Insights

**Why this section exists.** Users who have catalogued enough want to see what their collection is worth and how it has grown. This is a small section but a satisfying one, and it motivates thorough data entry.

**Who it is for.** Collectors and owners.

### Understand your collection statistics

- **Purpose.** Read and trust the per collection statistics.
- **Audience.** Collectors and owners.
- **Summary.** What the statistics screen shows for a collection: total items, estimated value, average per item, set completion, estimated value over time, acquisitions per month, breakdowns by category and by condition, value by location, and top items by value. Explain where each number comes from so the reader trusts it, for example that value over time is driven by each copy's acquisition date and value, and that a copy with no valuation counts as unvalued rather than zero. Note the screen invites more data entry when a collection is still thin.
- **Prerequisites.** Record what you paid and what it is worth; Track the copies you own.
- **Related pages.** The dashboard; Record what you paid and what it is worth.

### The dashboard

- **Purpose.** Explain the landing screen after sign in.
- **Audience.** Collectors and owners.
- **Summary.** What the dashboard surfaces: a greeting, member and pending invitation counts, and the recent activity feed across the account. Note that a fresh account without collections is sent to the getting started checklist instead.
- **Prerequisites.** The getting started checklist.
- **Related pages.** The activity feed and audit trail; Understand your collection statistics.

---

## Section 7: Collaboration

**Why this section exists.** KolleK accounts are multi user. Owners need to understand how to bring people in, at what access level, and how to change or remove that access later. The permission rules have real consequences (a viewer cannot edit, the last owner cannot be removed) that deserve clear documentation to prevent lockouts and surprises.

**Who it is for.** Account owners primarily, and anyone curious about what their role allows.

### Invite people to your account

- **Purpose.** Bring a new member into the account.
- **Audience.** Owners.
- **Summary.** Sending an invitation by email and choosing the role at invite time. What the invitee experiences: they open a link, and if they do not already have an account they set a name and password and join, verified and signed in. The important limits: invitations expire after seven days, only owners can invite, and someone who already has their own account cannot accept (because a person belongs to one account).
- **Prerequisites.** Accounts, users, and roles.
- **Related pages.** Manage members and roles; Understanding the three roles.

### Manage members and roles

- **Purpose.** Change what existing members can do, or remove them.
- **Audience.** Owners.
- **Summary.** Viewing members and pending invitations, changing a member's role, and removing a member. The critical safeguard, stated plainly: an account must always keep at least one owner, so the last owner cannot be demoted or removed. Warn that removing a member is not reversible from this screen.
- **Prerequisites.** Invite people to your account.
- **Related pages.** Accounts, users, and roles; Delete an account.

### Understanding the three roles in practice

- **Purpose.** A quick, scannable reference of who can do what.
- **Audience.** Owners and members.
- **Summary.** A table style page mapping viewer, editor, and owner to concrete capabilities (browse, create and edit catalogue content, manage types and tags and members, change account settings, delete the account). Reinforce that read access is open to any member including viewers, while writes require editor or owner and account administration requires owner.
- **Prerequisites.** Accounts, users, and roles.
- **Related pages.** Manage members and roles.

### Share a collection

- **Purpose.** Make a collection visible beyond yourself.
- **Audience.** Collectors and owners.
- **Summary.** Setting a collection's visibility to shared or public, and what each means for who can see it. The step to obtain and use the public link. Warn clearly about the consequence of public visibility: anyone with the link can view the collection without signing in. How to make it private again.
- **Prerequisites.** Visibility and sharing (concept).
- **Related pages.** Create and manage collections.

---

## Section 8: Your Account and Profile

**Why this section exists.** Separate from managing others, each user manages themselves: their name, language, preferences, and the account level settings an owner controls. Grouping these keeps personal settings distinct from collaboration.

**Who it is for.** Every user for the profile pages; owners for the account settings pages.

### Edit your profile

- **Purpose.** Update personal details and preferences.
- **Audience.** Every user.
- **Summary.** Editing first and last name, nickname, and email (noting disposable addresses are rejected), choosing an interface language, and toggling a 24 hour time format. Where recent activity and recent emails are surfaced on the profile.
- **Prerequisites.** Signing in.
- **Related pages.** Change your language; Your personal activity log.

### Set your avatar

- **Purpose.** Upload or remove a profile picture.
- **Audience.** Every user.
- **Summary.** Uploading an avatar and removing it, and where it appears. Brief and practical.
- **Prerequisites.** Edit your profile.
- **Related pages.** Edit your profile.

### Change your language

- **Purpose.** Switch the interface language.
- **Audience.** Every user.
- **Summary.** Choosing from the supported languages (English, French, Spanish, German, Brazilian Portuguese, Simplified Chinese, Japanese), noting the choice is per user, and that language can even be switched from the sign in page before logging in.
- **Prerequisites.** None beyond having an account.
- **Related pages.** Edit your profile.

### Your personal activity log and sent emails

- **Purpose.** Let a user review their own actions and the emails the system sent them.
- **Audience.** Every user.
- **Summary.** Where to find your full personal activity history and your sent email history, including delivery and bounce status. Frame it as a transparency and troubleshooting tool ("did that magic link email actually send").
- **Prerequisites.** Edit your profile.
- **Related pages.** The activity feed and audit trail; Troubleshooting email delivery.

### Account settings

- **Purpose.** Manage the account itself.
- **Audience.** Owners.
- **Summary.** Setting the account name and default currency, and re enabling the getting started checklist. Note that the default currency is a fallback that individual collections can override. Point to the deletion page for the destructive action rather than covering it here.
- **Prerequisites.** Accounts, users, and roles.
- **Related pages.** Collections (currency override); Delete an account.

---

## Section 9: Security

**Why this section exists.** KolleK holds records that matter to people, and it offers a genuinely rich set of account security controls. Users need clear, calm guidance on turning them on, and clear recovery paths so they do not lock themselves out. Security setup is deliberately separated from the "signing in" basics so the concepts get room to breathe.

**Who it is for.** Every user, with some owner only account safety pages.

### Security overview

- **Purpose.** Orient a user around the security controls available to them.
- **Audience.** Every user.
- **Summary.** A short map of the security surface: password, two factor authentication and recovery codes, magic links, API keys, and the login alert emails the system sends. Point to each detailed page. Set the tone that these are optional but recommended.
- **Prerequisites.** Signing in.
- **Related pages.** All pages in this section.

### Protect your account with two factor authentication

- **Purpose.** Turn on and manage two factor authentication.
- **Audience.** Every user.
- **Summary.** Setting up TOTP by scanning a QR code with an authenticator app and confirming a code, what changes at sign in afterwards, and disabling it. Emphasize saving the recovery codes. Explain the consequence that once enabled, password sign in requires a code, while magic link sign in does not prompt for one.
- **Prerequisites.** Security overview.
- **Related pages.** Save and use your recovery codes; Signing in.

### Save and use your recovery codes

- **Purpose.** Ensure a user can get back in if they lose their authenticator.
- **Audience.** Every user with two factor enabled.
- **Summary.** Where recovery codes are shown, that each code works once, and how to use one at the sign in challenge in place of a TOTP code. Stress storing them somewhere safe and offline.
- **Prerequisites.** Protect your account with two factor authentication.
- **Related pages.** Protect your account with two factor authentication.

### Magic links explained

- **Purpose.** Explain passwordless sign in and its trade offs.
- **Audience.** Every user.
- **Summary.** How to request a magic link, that it is valid for five minutes and sent to the account email, and the security note that magic link sign in bypasses the two factor step. When to prefer it and when not to.
- **Prerequisites.** Security overview.
- **Related pages.** Signing in; Protect your account with two factor authentication.

### Reset your password

- **Purpose.** Recover access when a password is forgotten, or change it deliberately.
- **Audience.** Every user.
- **Summary.** The forgot password flow (request a link, set a new password) and changing the password from profile security. Note the password strength requirements (minimum length and the breached password check) so a rejected password is not a mystery. Note the neutral "a link will be sent if the account exists" behavior so users are not confused by it.
- **Prerequisites.** Signing in.
- **Related pages.** Security overview.

### Login and security alert emails

- **Purpose.** Explain the unprompted security emails a user may receive.
- **Audience.** Every user.
- **Summary.** The alerts the system sends: a failed login attempt on an existing account, a new login or device, a change in IP address, and API key created or deleted notices. Frame them as "here is what these mean and what to do if one surprises you."
- **Prerequisites.** Security overview.
- **Related pages.** Your personal activity log and sent emails; Manage API keys.

### Manage API keys

- **Purpose.** Create and revoke personal access tokens for the API.
- **Audience.** Every user, leaning technical.
- **Summary.** Creating a labelled API key, the fact that the token is shown only once at creation, and revoking a key. Where keys are listed with their last used time. Point developers to the API section for how to actually use the token. Keep this page about the lifecycle, not the endpoints.
- **Prerequisites.** Security overview.
- **Related pages.** Authenticate with the API; Login and security alert emails.

---

## Section 10: Data Safety and Housekeeping

**Why this section exists.** Deleting things, recovering them, and understanding what is permanent are exactly the topics that generate anxious support requests. This section makes the reversible and the irreversible unmistakable, and covers the lifecycle actions that do not fit elsewhere.

**Who it is for.** Owners and editors, with the deletion pages relevant to every user.

### Restore something from the trash

- **Purpose.** Recover a soft deleted object and understand the trash.
- **Audience.** Owners and editors.
- **Summary.** What lands in the trash when deleted (collections, items, copies, categories, sets), how long it is kept before automatic purging (the configured retention period), how to restore an object back where it was, and how to empty the trash to delete permanently. State clearly which things do not go to the trash and are removed immediately (photos, documents, and the copy history records), and warn that emptying the trash is permanent.
- **Prerequisites.** Create and manage collections.
- **Related pages.** Delete your user; Delete an account.

### Delete your user

- **Purpose.** Let a person remove their own user.
- **Audience.** Every user.
- **Summary.** How to delete your own user from the profile danger zone, that an optional reason can be given, and that you are signed out afterward. Distinguish this clearly from deleting the whole account. Note the safety rule from the admin side that a user cannot be left as the account's only owner without handling ownership first.
- **Prerequisites.** Edit your profile.
- **Related pages.** Delete an account; Automatically delete an inactive user.

### Automatically delete an inactive user

- **Purpose.** Explain the optional self cleanup setting.
- **Audience.** Every user.
- **Summary.** The opt in preference that lets the system delete your user after a long period of inactivity, roughly six months, and how to turn it on or off from profile security. Be clear it is off unless chosen and applies only to the user who opted in.
- **Prerequisites.** Edit your profile.
- **Related pages.** Delete your user.

### Delete an account

- **Purpose.** Cover the most destructive action in the product.
- **Audience.** Owners.
- **Summary.** How an owner deletes the entire account, and the consequence stated as bluntly as possible: this removes every collection, item, copy, member, and all history, and it cannot be undone. Recommend exporting or backing up first where relevant, and point self hosters to the backup guidance.
- **Prerequisites.** Account settings.
- **Related pages.** Restore something from the trash; Back up and restore your instance.

### Back up your collection data

- **Purpose.** Tell a collector what portability exists today and set honest expectations.
- **Audience.** Collectors and owners.
- **Summary.** State plainly what can and cannot be exported from within the app: collection type definitions can be exported and imported as JSON, but there is no built in item or whole collection export yet. Point self hosters to the instance level database and file backups in the Self Hosting section as the real backup path today. This page exists to answer "how do I get my data out" honestly rather than leave the reader guessing.
- **Prerequisites.** Import and export a collection type.
- **Related pages.** Import and export a collection type; Back up and restore your instance.

---

## Section 11: Tutorials

**Why this section exists.** Concept and how to pages teach one thing at a time. Tutorials stitch them into realistic, end to end journeys so a reader can follow a believable scenario from start to finish. Each tutorial names its scenario, its prerequisites, its steps, the expected result, and sensible next steps, and each reuses a recurring collector persona for continuity.

**Who it is for.** New and intermediate collectors who learn best by following a complete example.

### Tutorial: Catalogue your first collection end to end

- **Purpose.** Take a reader from an empty account to a real, populated collection.
- **Audience.** New collectors.
- **Summary.** A full narrative: create a Comics collection, pick or adjust its type and custom fields, add a first item with a cover photo and custom field values, add a copy with a condition and location, record what was paid as a transaction, and add a first valuation. End by viewing the collection and its statistics. Call out the item versus copy distinction at the moment it first matters.
- **Prerequisites.** Create your account.
- **Related pages.** Create and manage collections; Add and edit items; Record what you paid and what it is worth.

### Tutorial: Set up your account for a specific hobby

- **Purpose.** Show how to tailor types, fields, locations, and tags before mass data entry.
- **Audience.** Collectors setting up seriously.
- **Summary.** Using a concrete hobby (for example vinyl records), design a collection type with grouped custom fields, build a nested location map (room, shelf, crate), and seed a useful tag vocabulary, so that adding many items later is fast and consistent. Show importing a type from JSON as a shortcut.
- **Prerequisites.** Tutorial: Catalogue your first collection end to end.
- **Related pages.** Set up collection types and custom fields; Set up your locations; Manage account tags.

### Tutorial: Track the full life of a valuable item

- **Purpose.** Demonstrate the copy history features working together on one prized copy.
- **Audience.** Collectors of higher value items.
- **Summary.** Follow one valuable copy through its whole story: record the acquisition transaction, add a professional valuation, insure it, attach the receipt and appraisal as documents, lend it to an exhibition and return it, log a conservation, and build its provenance narrative. Show how the timeline reads afterward. This tutorial ties Section 4 together.
- **Prerequisites.** Tutorial: Catalogue your first collection end to end; A copy's history explained.
- **Related pages.** Every page in Section 4.

### Tutorial: Invite your household or club and set permissions

- **Purpose.** Take an owner through collaborating safely.
- **Audience.** Owners.
- **Summary.** Invite two people at different roles, explain what each can and cannot do, share one collection publicly while keeping another private, and adjust a role afterward. Reinforce the last owner safeguard.
- **Prerequisites.** Accounts, users, and roles.
- **Related pages.** Invite people to your account; Manage members and roles; Share a collection.

### Tutorial: Self host KolleK with Docker

- **Purpose.** Take an operator from nothing to a running instance.
- **Audience.** Operators.
- **Summary.** The end to end Docker quick start: clone, copy the environment template, generate the application key, review passwords and the URL, start the stack, and create the first account. Then grant the first instance administrator. Cross reference the deeper configuration, upgrade, and backup pages rather than repeating them.
- **Prerequisites.** Self hosting overview.
- **Related pages.** Install with Docker; Configure your instance; Grant instance administrator access.

---

## Section 12: Developers and the API

**Why this section exists.** KolleK ships a full JSON API that mirrors the app, and a generated endpoint reference already exists at `/docs/api`. This section provides the narrative wrapper that the generated reference does not: how to authenticate, how the API is shaped, and where automation stands today. It must link to the generated reference for endpoint detail rather than restating it.

**Who it is for.** Developers building against an instance.

### API overview

- **Purpose.** Explain what the API is and point to the generated reference.
- **Audience.** Developers.
- **Summary.** That the API mirrors the web app one to one and enforces the same roles, that it is tenant scoped to the caller's account, and that the complete, always current endpoint reference lives at `/docs/api` (with Markdown at `/docs/api.md`). Explain the shape briefly: resources nest as account, collections, items, copies, with the copy history resources hanging off copies. Set the expectation that this section covers getting started and concepts, and the reference covers every endpoint.
- **Prerequisites.** How KolleK is organized.
- **Related pages.** Authenticate with the API; the generated reference at `/docs/api`.

### Authenticate with the API

- **Purpose.** Get a developer making their first authenticated request.
- **Audience.** Developers.
- **Summary.** Creating an API key in the app, sending it as a bearer token, and a first `GET` against a simple endpoint. Note registering or logging in through the API to obtain a token, including passing a two factor code when required, and revoking tokens. Include a runnable curl example. Point to the key lifecycle page for management.
- **Prerequisites.** Manage API keys; API overview.
- **Related pages.** Manage API keys; the generated reference at `/docs/api`.

### Rate limits and conventions

- **Purpose.** Save developers from avoidable surprises.
- **Audience.** Developers.
- **Summary.** The practical conventions worth stating once: authenticated requests are rate limited, list responses are paginated with a standard data, links, and meta envelope, money is handled in the smallest currency unit, and the same authorization rules as the web app apply (a forbidden action reads as not found). Point to the reference for per endpoint specifics.
- **Prerequisites.** Authenticate with the API.
- **Related pages.** API overview.

### Webhooks

- **Purpose.** Document webhook setup honestly, including its current limits.
- **Audience.** Developers.
- **Summary.** How to register a webhook endpoint (from profile settings or via the `beaver:create-webhook-endpoint` command), that each endpoint gets its own signing secret, and the signed payload shape a receiver should expect (an event name, a timestamp, and data, with a signature and timestamp header to verify). State clearly and up front that no application event currently triggers a webhook: the delivery machinery, signing, and retries are in place, but events will be wired up as the collection domain grows. Do not describe webhooks as a working automation feature today.
- **Prerequisites.** API overview.
- **Related pages.** Authenticate with the API.

---

## Section 13: Self Hosting and Administration

**Why this section exists.** Self hosting is a first class, supported use case, and it has real operational depth: Docker roles, the application key caveat that can destroy data if mishandled, upgrades, backups, and the instance wide administration panel. Operators need a dedicated, careful section. This is the most "developer and operator" part of the portal and should read like good infrastructure documentation.

**Who it is for.** Operators running an instance, and the instance administrator.

### Self hosting overview

- **Purpose.** Set expectations before an operator installs anything.
- **Audience.** Operators.
- **Summary.** What running an instance involves at a high level: a web role, a queue worker, and a scheduler, backed by a database, all from one Docker image. The requirements (Docker Engine version, resources). The single most important caveat introduced early: the application key must be set once and never changed on a running instance, or encrypted data and sessions become unreadable. Point to the installation walkthrough.
- **Prerequisites.** Cloud version versus self hosting.
- **Related pages.** Install with Docker; Configure your instance; The application key and encryption.

### Install with Docker

- **Purpose.** The authoritative installation guide.
- **Audience.** Operators.
- **Summary.** The full Docker Compose install: clone, copy the environment template, generate and set the application key, review the database passwords and public URL, and bring the stack up. Explain the three container roles and that only the web role runs migrations. Confirm success by opening the URL and creating the first account. Reference the existing `docker/README.md` as the source of truth and keep this page aligned with it.
- **Prerequisites.** Self hosting overview.
- **Related pages.** Configure your instance; Grant instance administrator access; Upgrade your instance.

### Configure your instance

- **Purpose.** Explain the environment variables an operator actually touches.
- **Audience.** Operators.
- **Summary.** Walk through the meaningful configuration groups rather than every variable: application identity and URL, the application key, the database connection, mail delivery (SMTP or Resend, defaulting to the log so email is visibly not sent until configured), file storage (local disk by default, with S3 compatible storage available), the trash retention period, the account deletion notification address, and whether the marketing site is shown. Note that sessions, cache, and the queue are database backed by default so no Redis is required. Point to the mail specific troubleshooting page.
- **Prerequisites.** Install with Docker.
- **Related pages.** Set up email delivery; The application key and encryption.

### Set up email delivery

- **Purpose.** Get transactional email actually sending.
- **Audience.** Operators.
- **Summary.** Why email matters (invitations, magic links, password resets, security alerts), that the default mailer only logs and sends nothing, and how to configure SMTP or Resend. How to verify delivery, including that the app records every email it sends with delivery and bounce status. Common failure signs.
- **Prerequisites.** Configure your instance.
- **Related pages.** Login and security alert emails; Troubleshooting email delivery.

### The application key and encryption

- **Purpose.** Explain the one setting that can irreversibly break an instance, and how encryption at rest works operationally.
- **Audience.** Operators.
- **Summary.** That sensitive fields are encrypted with the instance's application key, that the key must be identical across all containers and never changed on a running instance, and what breaks if it is (every encrypted column and all sessions become unreadable). How key rotation is supported through previous keys for operators who must rotate deliberately. Frame this as the single most important operational rule.
- **Prerequisites.** Self hosting overview.
- **Related pages.** Back up and restore your instance; How your data is protected.

### Upgrade your instance

- **Purpose.** Upgrade safely without losing data.
- **Audience.** Operators.
- **Summary.** The upgrade path: pull the newer version and rebuild, relying on forward only migrations that never reset data, since data lives in named volumes independent of the image. How to opt out of automatic migrations to run them manually. Call out the one time post upgrade maintenance step to rebuild the photo search index (`photos:rebuild-search-index`) so photo search keeps working after upgrading.
- **Prerequisites.** Install with Docker.
- **Related pages.** Back up and restore your instance; The application key and encryption.

### Back up and restore your instance

- **Purpose.** Protect and recover the data.
- **Audience.** Operators.
- **Summary.** What to back up (the database and the storage volume that holds uploaded photos and documents) and how, using a database dump and a file archive of the volume, as documented in `docker/README.md`. That there is no automated in app backup, so this is the operator's responsibility. The restore approach and the reminder that a restore is only useful with the matching application key. This is also the true "export my data" answer for self hosters.
- **Prerequisites.** Install with Docker; The application key and encryption.
- **Related pages.** Back up your collection data; Delete an account.

### Scheduled maintenance jobs

- **Purpose.** Tell an operator what the app does on its own.
- **Audience.** Operators.
- **Summary.** The daily scheduled jobs run by the scheduler container: purging trash past its retention period, flagging overdue loans, and deleting users who opted into inactivity deletion. Why the scheduler role must be running for these to happen. Reassure that these are safe and expected.
- **Prerequisites.** Install with Docker.
- **Related pages.** Restore something from the trash; Automatically delete an inactive user.

### Grant instance administrator access

- **Purpose.** Bootstrap and manage the server wide administrator.
- **Audience.** Operators.
- **Summary.** What the instance administrator flag is (server wide, separate from account roles, granting nothing extra inside one's own account) and how to grant or revoke it with the `beaver:make-instance-administrator` command. Why the panel answers as not found rather than forbidden to anyone without the flag. The safeguard that an administrator cannot revoke their own flag or delete their own user, so an instance cannot be locked out.
- **Prerequisites.** Install with Docker; Accounts, users, and roles.
- **Related pages.** The instance administration panel.

### The instance administration panel

- **Purpose.** Document what the instance wide panel can do.
- **Audience.** Instance administrators.
- **Summary.** The overview metrics across all accounts (counts of accounts, users, collections, items, and signups over time), browsing and searching accounts (by member email, since names are encrypted), viewing an account's members and recent activity, and the destructive actions: deleting an entire account, deleting a user, and toggling another user's administrator flag. State that this panel is web only by design and has no API. Flag that the Support and Reviews areas of the panel are not built yet.
- **Prerequisites.** Grant instance administrator access.
- **Related pages.** Grant instance administrator access; Delete an account.

### Administer with the command line

- **Purpose.** Reference the artisan commands an operator may need.
- **Audience.** Operators.
- **Summary.** A short reference of the operator relevant commands: granting or revoking instance administrator, creating a webhook endpoint, rebuilding the photo search index, and syncing translation keys when adding a language. Note which are for day to day operation versus development only, and cross reference the relevant pages.
- **Prerequisites.** Install with Docker.
- **Related pages.** Grant instance administrator access; Upgrade your instance; Add a language.

### Add a language

- **Purpose.** Explain how the interface is translated and how to add or complete a locale.
- **Audience.** Operators and contributors.
- **Summary.** That the interface ships in seven languages, that translations live as one file per locale, and how the key extraction command scaffolds a new locale for translation. Note the current gap that the marketing and API documentation strings are not yet translated and fall back to English. This page straddles operators and contributors.
- **Prerequisites.** Configure your instance.
- **Related pages.** Change your language; Administer with the command line.

---

## Section 14: Troubleshooting and FAQ

**Why this section exists.** A good portal catches the questions users ask when something does not behave as expected, reducing support load. These pages are answer first and scannable, and they cross link into the detailed pages elsewhere.

**Who it is for.** Everyone, split by audience per page.

### Troubleshooting sign in

- **Purpose.** Resolve the most common access problems.
- **Audience.** Collectors.
- **Summary.** Forgotten password, lost two factor device (use a recovery code), a magic link that expired (they last five minutes), being locked out after repeated failed attempts, and why an invitation link might not work (expired, or the email already has an account). Each with the concrete fix and a link to the fuller page.
- **Prerequisites.** Signing in.
- **Related pages.** Reset your password; Save and use your recovery codes; Magic links explained.

### Troubleshooting email delivery

- **Purpose.** Explain why expected emails do not arrive.
- **Audience.** Operators, and owners wondering why an invite did not land.
- **Summary.** The most common cause on a fresh instance (mail is only being logged, not sent, until configured), how to check the recorded sent emails and their delivery or bounce status, and where to configure a real mailer. Point owners at the operator page when the fix is server side.
- **Prerequisites.** None.
- **Related pages.** Set up email delivery; Your personal activity log and sent emails.

### Frequently asked questions

- **Purpose.** One scannable page for the recurring conceptual questions.
- **Audience.** Everyone.
- **Summary.** Short answers to the questions the model and behavior naturally raise, each linking to the authoritative page. Candidates grounded in real behavior: what is the difference between an item and a copy; can I belong to more than one account (no, one user is one account); is it really free (self hosting is free, there is no in app billing); how do I get my data out (type export today, full backups via self hosting); why can I not delete the last owner; where is the search feature; do webhooks work yet; is my data encrypted and what does that protect; can I add my own conditions today (API only for now). Keep answers honest about current limits.
- **Prerequisites.** None.
- **Related pages.** Many, per answer.

### Feature status and roadmap

- **Purpose.** A single honest page listing what is planned or partial, so these facts live in one maintained place instead of being scattered.
- **Audience.** Everyone.
- **Summary.** Clearly separated "available now" versus "not yet" list, covering: global web search (placeholder today), webhook event delivery (machinery only), custom condition management on the web (API only), item and collection import and export (types only), and the instance Support and Reviews areas (stubs). Frame as transparency, and keep it updated as the product grows. This page also protects the rest of the portal from over promising.
- **Prerequisites.** None.
- **Related pages.** Webhooks; Back up your collection data; The instance administration panel.

---

## Section 15: Reference

**Why this section exists.** A few pages are pure lookup material that readers scan rather than read. Keeping them at the end, factual and complete, keeps the task oriented sections uncluttered.

**Who it is for.** Everyone, as needed.

### Glossary

- **Purpose.** Define every product term in one place.
- **Audience.** Everyone.
- **Summary.** One line definitions for account, user, role, collection, collection type, custom field, field group, item, copy, category, set, series, tag, location, condition, transaction, valuation, insurance record, loan, maintenance record, provenance event, location history, document, visibility, trash, instance administrator, API key, and webhook. Each entry links to its concept or how to page. This is the fast disambiguation reference the whole portal can point back to.
- **Prerequisites.** None.
- **Related pages.** All concept pages.

### Field and status reference

- **Purpose.** A complete lookup of the enumerated choices a user meets in forms.
- **Audience.** Collectors and developers who want the full lists.
- **Summary.** The complete option sets in one scannable place: copy statuses, transaction types, valuation types and confidence levels, insurance statuses, loan directions and statuses, maintenance types, provenance event types and date precision levels, document types, custom field types, and collection visibility. For each, a one line meaning. This backs up the how to pages without cluttering them.
- **Prerequisites.** The relevant concept pages.
- **Related pages.** Section 4 pages; Set up collection types and custom fields.

### Emails KolleK sends

- **Purpose.** A single catalogue of every transactional and alert email.
- **Audience.** Owners and operators.
- **Summary.** Each email the system can send, when it is triggered, and who receives it: account invitation, magic link, email verification, password reset, failed login alert, new login or device alert, IP change alert, API key created and deleted notices, and the internal account and user deletion notices. Useful for operators verifying delivery and for owners recognizing a legitimate message.
- **Prerequisites.** None.
- **Related pages.** Set up email delivery; Login and security alert emails.

---

## Recommended reading order (summary)

For a collector: What is KolleK, Create your account, The getting started checklist, How KolleK is organized, Items versus copies, then the Core Features how to pages, then the copy history section as their tracking needs grow.

For an owner: everything a collector reads, plus Accounts users and roles, the Collaboration section, and Account settings and Data Safety.

For an operator: Cloud versus self hosting, then the whole Self Hosting and Administration section, starting from the overview and the application key caveat.

For a developer: API overview, Authenticate with the API, then the generated reference at `/docs/api`, with Webhooks read as "not yet firing."

---

## Notes for the writing agent

- Prefer goal titles over feature titles, and always explain why and when, not only how, per the writer skill.
- Reuse a small cast of collector personas across tutorials and examples for continuity.
- Every destructive action (deleting a collection, item, account, user, emptying the trash, making a collection public) must state its consequence plainly, in line with the project rule to warn before destructive actions.
- Respect the project writing rule for documentation: do not use dashes as punctuation. Rephrase with periods, commas, or parentheses.
- Never present a planned or stubbed capability as finished. The Feature status page is the maintained home for those facts, and other pages should link to it rather than each hedging separately.
- Link to the generated API reference at `/docs/api` for endpoint detail rather than restating endpoints in prose.
