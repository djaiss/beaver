# Features Marketing Audit

## Recommendation

Build a **Features** hub plus twelve focused feature pages. Lead with the outcome that KolleK can make a physical collection understandable, protected, and manageable over time -- not merely catalogued.

The clearest positioning is:

> A private home for every object you collect, with the detail and history each physical copy deserves.

The marketing site currently foregrounds flexible fields and item/copy tracking. Those are good foundations, but the audit finds a more distinctive story in the shipped product: KolleK connects a copy's purchase, current value, insurance, loans, maintenance, provenance, location, and documents into one chronological record. That capability should be the center of the Features section.

## Tone Reset: Serious Work, With A Sense Of Humour

The future Features section should follow the pricing page's spirit, not its exact jokes: direct, opinionated, occasionally ridiculous, and completely clear about what the product actually does. The feature is the straight person; the surrounding copy gets to have a little fun.

The useful lesson from the current pricing page is its contrast between honest product facts and lines such as "honestly, do this one", "no monthly nibbling", and "reset the abacus." PostHog follows a similar pattern: substantial capability claims sit next to deliberately un-corporate labels and self-aware CTAs. KolleK should borrow that *rhythm*, not their wording or their technical-product voice.

### Voice Rules

1. **Be specific before being funny.** State the capability in plain language, then add the wink. `Track every copy, including its condition, value, and hiding place.` is better than a joke that obscures the feature.
2. **Write like a collector, not a SaaS brochure.** Say `the box in the attic`, `the shelf you reorganized three times`, or `the receipt you will definitely need later`, when the scenario supports it.
3. **Use one joke per content block.** A playful eyebrow, aside, CTA, or footnote is enough. Dense humour will make valuable-object and security pages feel frivolous.
4. **Let the visuals carry some of the personality.** Small labels, realistic item names, satisfying empty states, and playful microcopy can create warmth without making every headline perform.
5. **Keep trust claims sober.** Encryption, insurance, money, loans, backups, security, and feature availability should be exact. The lightness belongs around the fact, never in place of it.
6. **Prefer self-aware confidence to hype.** KolleK can say what it is good at, admit what is unfinished, and make that honesty part of the voice.

### Copy Mechanics

| Element | Straight version | KolleK version |
| --- | --- | --- |
| Eyebrow | Copy tracking | `FOR PEOPLE WITH MORE THAN ONE OF THE SAME THING` |
| Headline | Track every physical copy | `Yes, the duplicate is a different creature.` |
| Supporting text | Track condition and location per copy | `Give each copy its own condition, location, and backstory. Because "somewhere in the house" is not a location.` |
| CTA | View copy tracking | `Meet your duplicates` |
| Reassurance | Data is encrypted at rest | `Encrypted at rest. Curious database thieves get gibberish.` |
| Honest limitation | Global search is not available | `Global search is still on the bench. In-collection filtering is already playing.` |

Do not use a joke merely to make a page feel lighter. The best source material is the collector's real, affectionate problem: duplicates, mystery boxes, incomplete sets, misplaced receipts, overloaded shelves, and the odd object that has become unexpectedly valuable.

## What To Emphasize

Prioritize these messages across the hub, navigation, and first six pages:

1. **Know every physical copy, not just the title.** The item/copy model is a real product distinction for collectors with variants, duplicates, graded items, or valuable objects.
2. **Keep the whole story with the object.** Copy history turns scattered receipts, notes, spreadsheets, and memory into a readable timeline.
3. **Make the catalogue fit the hobby.** Collection types, reusable custom fields, groups, and ready-made templates make the product credible for very different collector categories.
4. **See the value and where it lives.** Valuations, acquisition records, statistics, conditions, and storage locations create practical answers from the catalogue.
5. **Own the data and the deployment.** Self-hosting, encrypted storage, Docker, backups, and the API appeal to privacy-conscious and technical collectors.
6. **Use it with the people who share the collection.** Invitations, owner/editor/viewer roles, and audit trails are strong supporting proof for households, clubs, and businesses.

Do not lead with generic features such as tags, individual photos, or settings. They should strengthen the main pages rather than compete with the primary story.

## Information Architecture

Create `/features` as a visual index, then use the following twelve child pages. The first six should receive the largest production budget and be linked from the home page.

| Priority | Page | Core buyer benefit | Suggested route |
| --- | --- | --- | --- |
| P1 | Every copy, accounted for | Distinguish, locate, and understand every physical item owned | `/features/copy-tracking` |
| P1 | A complete object history | Keep money, custody, care, provenance, and paperwork together | `/features/copy-history` |
| P1 | Made for your kind of collection | Record the details that matter to comics, watches, wine, cards, and more | `/features/custom-catalogues` |
| P1 | Know what the collection is worth | Turn recorded facts into trusted value and growth insights | `/features/collection-insights` |
| P1 | Your collection, in your control | Self-host, encrypt, back up, and keep ownership of the data | `/features/data-ownership` |
| P1 | Collect together | Give a household, club, or team the right access without losing accountability | `/features/collaboration` |
| P2 | Find and organize what you own | Browse visually or compare precisely with organization tools that scale | `/features/organization` |
| P2 | See the collection, not just a list | Manage covers, photo libraries, and visual browsing | `/features/photos-and-browsing` |
| P2 | Protect valuable objects | Record insurance, service, condition, and storage context | `/features/protection-and-care` |
| P2 | Build on your catalogue | Use the complete JSON API and personal API keys | `/features/api` |
| P3 | Built to run anywhere | Install and operate a full instance with Docker | `/features/self-hosting` |
| P3 | Safe by default | Use encryption at rest, two-factor authentication, magic links, recovery codes, and security alerts | `/features/security` |

## Page Briefs

### 1. Every Copy, Accounted For

**Job to be done:** Help a collector answer, "Which exact one do I own, what shape is it in, and where is it?"

**Eyebrow:** `FOR WHEN “I THINK I HAVE TWO” IS NOT A SYSTEM`

**Headline direction:** `Yes, the duplicate is a different creature.`

**Supporting copy:** `Track duplicates, variants, graded editions, and individual objects without turning your catalogue into a maze of near-identical entries.`

**Visual sequence:**

1. Item page showing one title with several distinct copies.
2. Close-up copy cards showing condition, location, acquisition date, and estimated value.
3. A before/after comparison: one generic title record versus the specific copy records KolleK keeps.
4. A lightweight animation switching between copies while the history and location change.

**Proof points:** Per-copy condition, storage location, acquisition detail, valuation, and status; multiple copies beneath one catalogued item.

**Primary CTA:** `Meet your duplicates`

### 2. A Complete Object History

**Job to be done:** Replace separate receipts, service notes, loan messages, and memory with one trustworthy story.

**Eyebrow:** `RECEIPTS HAVE A HABIT OF GOING WALKABOUT`

**Headline direction:** `Keep the good story. Lose the paper chase.`

**Supporting copy:** `From the first purchase to the latest valuation, keep the details that make an object more than an entry in a list.`

**Visual sequence:**

1. Full-width copy timeline with transactions, valuation, insurance, service, loan, provenance, move, and document events.
2. Split view: a receipt/document attached to a transaction beside the matching timeline event.
3. Timeline filters moving from the meaningful story to the complete operational record.
4. A single valuable-object case study, such as a watch or rare comic, told as a short chronological sequence.

**Proof points:** Separate dated records are merged into a timeline; records remain attributable to the specific physical copy; routine and meaningful event views exist.

**Primary CTA:** `Follow the plot`

### 3. Made For Your Kind Of Collection

**Job to be done:** Give collectors a system that speaks their category's language rather than forcing a generic inventory template.

**Eyebrow:** `NO, A WATCH IS NOT JUST “THING #84”`

**Headline direction:** `Your hobby has jargon. We came prepared.`

**Supporting copy:** `Start with ready-made collection types, then shape the fields, groups, and choices around what you actually care about.`

**Visual sequence:**

1. Type picker with several real collection categories: comics, vinyl, cards, books, watches, wine, coins, and games.
2. Type editor adding fields and arranging them into groups.
3. Same KolleK item form shown in two category-specific versions, for example a comic and a wine bottle.
4. Collection configuration showing several enabled types for one collection.

**Proof points:** Twelve ready-made types; text, number, date, yes/no, select, and rating fields; reusable account-wide types; field groups; type definition JSON import/export.

**Primary CTA:** `Teach KolleK your hobby`

### 4. Know What The Collection Is Worth

**Job to be done:** Help a collector see value, growth, concentration, and missing information without maintaining a separate spreadsheet.

**Eyebrow:** `SPREADSHEETS HAVE HAD A GOOD RUN`

**Headline direction:** `Finally, numbers that know what they are talking about.`

**Supporting copy:** `Record what you paid and what each copy is worth today. KolleK turns the history into a collection view you can trust.`

**Visual sequence:**

1. Statistics overview with total value, item count, copy count, and average value.
2. Value-over-time and acquisitions charts with short callouts explaining their data source.
3. Breakdown visual for value by storage location and condition distribution.
4. Top-items panel connecting a high-value item back to its latest valuation.

**Proof points:** Current value derives from the latest valuation; acquisition date derives from the earliest acquiring transaction; twelve-month value and acquisition charts; category, condition, and location breakdowns; set completion.

**Primary CTA:** `Show me the numbers`

### 5. Your Collection, In Your Control

**Job to be done:** Reassure collectors who do not want sensitive ownership, valuation, and location data trapped in a consumer service.

**Eyebrow:** `YOUR COLLECTION. YOUR SERVER. YOUR SLIGHTLY OVER-SPECIFIED NAS.`

**Headline direction:** `Keep your collection where you can point at it.`

**Supporting copy:** `Run KolleK yourself, keep sensitive fields encrypted at rest, and decide where your data, photos, and documents live.`

**Visual sequence:**

1. Clean self-hosting architecture diagram: web, queue, scheduler, database, storage volume.
2. A privacy-oriented product screen with collection data, value, and location details, paired with an encryption-at-rest callout.
3. Backup illustration showing database, uploads, and application key as the three inputs to protect.
4. Short installation terminal sequence using Docker Compose.

**Proof points:** Supported Docker deployment; encrypted sensitive fields at rest; database-backed services with optional S3-compatible storage; documented backup and restore process; MIT-licensed source.

**Primary CTA:** `Put it on your hardware`

### 6. Collect Together

**Job to be done:** Give a group one source of truth without handing every member the keys to the catalogue.

**Eyebrow:** `FOR HOUSEHOLDS, CLUBS, AND THE ONE PERSON WHO ALWAYS EDITS THE WRONG FIELD`

**Headline direction:** `Everyone can help. Not everyone needs the big red button.`

**Supporting copy:** `Invite the people who share the collection and give each one the role that matches how they contribute.`

**Visual sequence:**

1. Member list with owner, editor, and viewer roles.
2. Invite flow from invitation email to first access.
3. A simple permissions matrix with real collection tasks, not abstract labels.
4. Activity feed showing a clear record of who changed an item and when.

**Proof points:** Email invitations; owner/editor/viewer roles; permissions enforced on writes; account and item-level activity history; actor names preserved in the history.

**Primary CTA:** `Bring in the crew`

### 7. Find And Organize What You Own

**Job to be done:** Make a growing collection navigable by the way a collector naturally thinks about it.

**Eyebrow:** `THE SHELF IS NOT A DATABASE. SADLY.`

**Headline direction:** `Organize it your way. Then find it again.`

**Supporting copy:** `Organize by category, set, series, tag, condition, and location, then browse the way that makes sense for the question in front of you.`

**Visual sequence:**

1. Collection grid, list, and table view toggles using the same items.
2. Nested storage-location tree from room to cabinet to shelf.
3. Set-completion progress paired with a category/series view.
4. Item tags and in-collection filtering in use.

**Proof points:** Per-user, per-collection view preferences; grid, list, and table layouts; nested categories and locations; sets, series, tags, and reusable condition scales; in-collection filter.

**Claim guardrail:** Do not call the current filter global search. Account-wide search is explicitly not available yet.

**Primary CTA:** `Tidy the chaos`

### 8. See The Collection, Not Just A List

**Job to be done:** Serve collectors who recognize their items by covers, labels, packaging, and visual details.

**Eyebrow:** `BECAUSE YOU RECOGNIZE THE COVER BEFORE THE CATALOGUE NUMBER`

**Headline direction:** `A collection should look this good on screen, too.`

**Supporting copy:** `Attach several photos to an item, choose the cover that represents it best, and manage the entire image library from one place.`

**Visual sequence:**

1. Visual collection grid built from real item covers.
2. Item gallery with cover selection and image reordering.
3. Photo library showing search, cover/extra filtering, and size sorting.
4. Photo detail panel with filename, format, dimensions, size, uploader, and linked item.

**Proof points:** Multiple photos per item; main cover selection and ordering; account-wide photo library; photo search, filtering, size sorting, and bulk deletion.

**Primary CTA:** `Give it some shelf appeal`

### 9. Protect Valuable Objects

**Job to be done:** Give collectors a practical record for safeguarding, maintaining, and accounting for high-value objects.

**Eyebrow:** `FOR THE OBJECTS THAT WOULD MAKE A BAD DAY WORSE`

**Headline direction:** `Keep the important details close. Very close.`

**Supporting copy:** `Keep insurance, condition, care, custody, storage, and proof of ownership beside the physical copy they describe.`

**Visual sequence:**

1. Copy history tabs highlighting Insurance, Maintenance, Loans, Locations, and Documents.
2. Insurance record connected to the latest valuation.
3. Service record that shows condition before and after maintenance.
4. Loan and return flow showing counterparty, dates, and returned condition.

**Proof points:** Insurance records, dated maintenance, loans and returns, condition changes, storage moves, attached files and external links, and provenance events.

**Primary CTA:** `Give valuables a paper trail`

### 10. Build On Your Catalogue

**Job to be done:** Give technical users confidence that their catalogue can participate in their own workflows and tooling.

**Eyebrow:** `FOR PEOPLE WHO LOOK AT A CATALOGUE AND THINK “API”`

**Headline direction:** `Your collection can leave the app. Politely.`

**Supporting copy:** `Use a documented JSON API to read and manage the catalogue from your own tools, scripts, and integrations.`

**Visual sequence:**

1. Generated API documentation screen with endpoint navigation.
2. Compact request/response example for a collection or item.
3. API-key creation screen showing a key is revealed once.
4. Conceptual workflow from KolleK API to a private script or dashboard.

**Proof points:** Token-authenticated API mirrors the application; generated endpoint reference; pagination, rate limits, and predictable API conventions; personal API keys.

**Claim guardrail:** Do not market event-driven integrations or live webhooks. Endpoints can be registered and signed, but no product events currently fire webhook deliveries.

**Primary CTA:** `Open the toolbox`

### 11. Built To Run Anywhere

**Job to be done:** Make self-hosting feel deliberate and approachable rather than a side project.

**Eyebrow:** `SOME ASSEMBLY REQUIRED. NOT MUCH, THOUGH.`

**Headline direction:** `Your own collection app. On your own terms.`

**Supporting copy:** `Start with Docker Compose, keep the operational footprint small, and retain a clear path for upgrades and backups.`

**Visual sequence:**

1. Four-step install path from clone to running instance.
2. Deployment composition showing web, queue, scheduler, MySQL, and durable storage.
3. Upgrade sequence emphasizing preserved volumes and migrations.
4. Backup checklist rendered as database, uploads, and application key.

**Proof points:** Docker image and Compose stack; MySQL; queue and scheduler roles; local or S3-compatible storage; documented upgrades and instance administration.

**Primary CTA:** `Run the thing`

### 12. Safe By Default

**Job to be done:** Show that a collection of valuable objects should receive real account protection without making security the only product story.

**Eyebrow:** `A LITTLE PARANOIA IS JUST GOOD HOUSEKEEPING`

**Headline direction:** `Keep the keys to the collection in good hands.`

**Supporting copy:** `Add a second factor, use passwordless sign-in when it suits you, and receive alerts when account activity needs attention.`

**Visual sequence:**

1. Two-factor setup with authenticator QR code and recovery codes.
2. Magic-link email and secure sign-in state.
3. Security-alert email examples for new login, changed IP, and API key changes.
4. Compact security-settings overview.

**Proof points:** Two-factor authentication, recovery codes, passwordless magic links, breached-password checking, API key alerts, and new-login/IP-change notifications.

**Primary CTA:** `Lock it down`

## Transparency Footer On Every Feature Page

Every feature page ends with an intentionally candid, full-width comparison block. It follows the structure in the provided PostHog reference: two equal columns, strong horizontal rules between rows, a plainspoken "not for you" column on the left, and a specific "choose KolleK when" column on the right.

**Section framing:**

- Left heading: `KolleK might not be for you (yet) if...`
- Right heading: `Choose KolleK when...`
- Optional small line beneath the section on pages with a known limitation: `We will update this page when the product changes. The feature status page has the boring-but-important details.`

**Layout rules:**

- Place after the main page CTA and before the site footer.
- Keep it text-led: no card grid, badges, illustrations, or decorative comparison checkmarks.
- Use 2-3 rows per column; a row begins with a bold, direct statement and may end with lighter italic context.
- On mobile, stack the "might not be" column first so the visitor sees the caveat before the pitch.
- Treat the left column as a real recommendation, not a straw man. If a user belongs there, the page should genuinely help them choose something else.

### 1. Every Copy, Accounted For

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| You only need a list of titles. *One row per thing is plenty.* | You own duplicates, variants, editions, or graded objects. *Each physical copy gets its own record.* |
| You do not care which exact copy you own. *The details can stay fuzzy.* | Condition, location, value, and acquisition details matter per object. *Because two copies are rarely interchangeable.* |
| You need barcode-driven retail inventory. | You want a collector's record of ownership, not a stockroom count. |

### 2. A Complete Object History

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| A current value and a single note are enough. *No need to keep the old story.* | You want purchases, valuations, care, loans, moves, and documents in one timeline. |
| You need legal chain-of-custody or specialist appraisal software. | You need a practical, readable history of the object you own. |
| You prefer paperwork spread across several folders. *Brave choice.* | You would like the receipt, the record, and the object to agree with each other. |

### 3. Made For Your Kind Of Collection

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| A fixed, genre-specific database already contains every field you need. | Your collection has its own vocabulary, and you want the catalogue to learn it. |
| You never need to add a new field, option, or grouping. | You want reusable types, custom fields, and forms that make sense to your hobby. |
| You want a massive public reference database maintained for you. | You want to own the structure of the data you collect. |

### 4. Know What The Collection Is Worth

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| You need live market prices supplied automatically. | You want your own transactions and valuations to become useful collection insights. |
| You want speculative estimates without recording where they came from. | You want a clear distinction between what you paid and what a copy is worth now. |
| You only need a basic total. | You want value over time, acquisition trends, top items, location value, and completion progress. |

### 5. Your Collection, In Your Control

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| You do not want any responsibility for hosting, upgrades, or backups. | You want to decide where the app, data, photos, and documents live. |
| You need end-to-end encryption where the operator cannot access application data. | You want sensitive fields encrypted at rest and control over the encryption key. |
| You expect an automated in-app backup button. | You are willing to make real backups of the database, storage, and application key. |

### 6. Collect Together

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| You need complex enterprise identity, SSO, or approval workflows. | A clear owner/editor/viewer model is enough for your household, club, or small team. |
| You need public collection links today. *Visibility settings exist, but public links do not yet.* | You want to invite trusted people and keep a readable record of changes. |
| You want everyone to edit everything. | You want people to help without handing everyone the big red button. |

### 7. Find And Organize What You Own

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| You need global search across every collection today. *It is planned, not shipped.* | You want focused in-collection filtering and several ways to browse what is already in front of you. |
| One flat alphabetical list is your ideal organization system. | Categories, locations, sets, series, tags, and conditions reflect how you think about the collection. |
| You need warehouse-grade barcode workflows. | You need to know which shelf, box, room, or category an object belongs to. |

### 8. See The Collection, Not Just A List

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| One tiny thumbnail per title is enough. | Covers, labels, packaging, and detail shots help you recognize what you own. |
| You need professional digital-asset-management workflows with advanced image editing. | You want each item's photos and metadata connected to the catalogue it belongs to. |
| You never need to find a photo outside its item page. | You want one account-wide photo library with search, filters, and bulk cleanup. |

### 9. Protect Valuable Objects

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| You need insurance claims handling or policy administration software. | You want insurance details, valuations, documents, and the object itself in one place. |
| You need regulated conservation or museum collections software. | You want a practical log of care, condition, custody, storage, and provenance. |
| A note saying "in the safe" is enough. | You want to know where it is, what happened to it, and what supports its value. |

### 10. Build On Your Catalogue

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| You need turnkey integrations with every service under the sun. | You want a documented JSON API you can use with your own tools and scripts. |
| You need event-driven webhooks today. *Endpoints and signing exist, but no product event fires them yet.* | You need direct, token-authenticated access to the catalogue now. |
| You would rather never see an API token. | You enjoy a useful escape hatch when a spreadsheet or another tool needs your data. |

### 11. Built To Run Anywhere

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| You want a zero-maintenance hosted service and never want to see Docker. | You want a documented, supported route to running the full app on infrastructure you choose. |
| You need a deployment platform to make every operational decision for you. | You are comfortable following a short install, upgrade, and backup guide. |
| You need a large enterprise operations stack. | A small server, Docker Compose, and a clear operating model sound like good news. |

### 12. Safe By Default

| KolleK might not be for you (yet) if... | Choose KolleK when... |
| --- | --- |
| You require SSO, hardware security keys, or a full enterprise identity platform. | You want strong everyday account protection without turning setup into a project. |
| You need end-to-end encryption. | You want two-factor authentication, recovery codes, magic links, and actionable security alerts. |
| You expect security features to be invisible until something goes wrong. | You want a straightforward settings page that makes good protection easy to turn on. |

## Features Hub Brief

The hub should be a real decision page, not a flat card directory.

1. **Hero:** State the umbrella outcome: `Everything you collect, understood in full.` Pair it with a real product montage: visual collection, copy detail, and timeline.
2. **Primary story strip:** Use the progression `Catalogue -> Track each copy -> Preserve its history -> See its value -> Keep control` to make the product model obvious in seconds.
3. **Six P1 entries:** Give each a substantial preview with at least one authentic product visual, one benefit-led headline, one supporting sentence, and a route into the full page.
4. **Supporting capabilities:** Present the remaining six as denser navigation entries, each still using a real UI capture or purposeful diagram.
5. **Trust band:** Place open source, self-hosting, encryption at rest, API, and language support together as proof that the product is built for long-term ownership.
6. **CTA:** Close on the product's two legitimate adoption paths: self-hosting and the hosted offering, without burying the distinction.

## Visual Production Rules

- Use real, populated product captures whenever the UI is the proof. Do not use empty dashboards or generic device mockups in place of the product.
- Build each page around one collector scenario. Use a consistent example object throughout a page so the visual sequence reads as a story.
- Mix capture types: broad screen, focused detail, annotated close-up, and data-flow or architecture diagram. A page with four slightly different full-screen shots will feel repetitive.
- Annotate only the outcome-bearing details, such as a newly derived value, the link between a receipt and a copy, or a change in custody. Keep callouts minimal and factual.
- Use category-specific examples throughout the section, but do not create separate feature pages for every hobby. The custom-catalogue page should prove range efficiently.
- Keep all claims tied to a visible, shipped capability. The application has a maintained feature-status document, and the marketing site should treat it as the source of truth before publishing feature copy.

## Claim Boundaries

The audit found visible groundwork for several features that must not be presented as available:

| Do not claim | Accurate replacement |
| --- | --- |
| Global search across every collection | In-collection filtering and photo-library search are available; global search is planned. |
| Public links or enforced collection sharing | Collection visibility settings are stored, but no public link exists and visibility is not enforced yet. |
| Working outbound webhooks or event integrations | Webhook endpoints and signing are available, but no application events trigger deliveries yet. |
| Full item or collection import/export | Only collection-type definitions can currently be imported or exported; the API and instance backups are the current data-access paths. |
| Automated in-app backup | Self-hosters can create complete instance backups following the documented database, storage, and encryption-key process. |

## Evidence Reviewed

This recommendation is based on the current application route surface, Blade views and services, the homepage, README, and the English product documentation. In particular, the documentation confirms the available feature set and explicitly identifies unfinished global search, sharing enforcement, webhook delivery, and item/collection import/export.

Key implementation evidence includes the copy-history builder, collection statistics service, API documentation generator, self-hosting documentation, and the product feature-status reference. This is an editorial and information-architecture recommendation only; no product capability or marketing route was changed.
