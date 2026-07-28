<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * The questions and answers shown on the public FAQ page. They live here rather
 * than in the view because there are a hundred of them, and because the page
 * needs to count them (the hero line, the table of contents) before it renders
 * a single one.
 *
 * Every answer is written against what the application actually does today, and
 * says so plainly when the answer is "not yet". The other honest inventory is
 * the feature status page in the documentation portal; when one moves, the other
 * has to move with it.
 *
 * The copy is resolved through __() at call time, so it follows the locale of
 * the request instead of being frozen at boot.
 */
class MarketingFaq
{
    /**
     * The ten short answers shown above the full list. They repeat the questions
     * people ask before anything else, with a two word verdict and one line of
     * detail.
     *
     * @return array<int, array{question: string, verdict: string, note: string}>
     */
    public function quickAnswers(): array
    {
        return [
            [
                'question' => __('Is KolleK really open source?'),
                'verdict' => __('Yes, MIT'),
                'note' => __('The whole application is on GitHub. Read it, fork it, run it.'),
            ],
            [
                'question' => __('Is self-hosting free?'),
                'verdict' => __('Free, forever'),
                'note' => __('No account with us, no licence key, no paid tier.'),
            ],
            [
                'question' => __('Do I own my data?'),
                'verdict' => __('It stays yours'),
                'note' => __('You keep ownership of everything you put in.'),
            ],
            [
                'question' => __('Is my collection private?'),
                'verdict' => __('Account only'),
                'note' => __('There is no public link yet, so nothing leaves your account.'),
            ],
            [
                'question' => __('Is there a subscription?'),
                'verdict' => __('No, one payment'),
                'note' => __('Self-hosting is free. A hosted account is unlocked once, never rented.'),
            ],
            [
                'question' => __('Can I export my data?'),
                'verdict' => __('Only partly'),
                'note' => __('Type definitions and open loans. Item export is not built yet.'),
            ],
            [
                'question' => __('Can I invite other people?'),
                'verdict' => __('Yes, no seat fee'),
                'note' => __('Owner, editor and viewer roles, enforced on every write.'),
            ],
            [
                'question' => __('What if I stop using KolleK?'),
                'verdict' => __('It keeps running'),
                'note' => __('A self-hosted instance needs nothing from us to work.'),
            ],
            [
                'question' => __('Where is my data hosted?'),
                'verdict' => __('Wherever you put it'),
                'note' => __('Your own server, or our hosted infrastructure if you use it.'),
            ],
            [
                'question' => __('Is it appraisal software?'),
                'verdict' => __('No'),
                'note' => __('It records the values you enter. It never sets them.'),
            ],
        ];
    }

    /**
     * Every section, in the order they appear on the page.
     *
     * @return array<int, array{id: string, title: string, blurb: string, items: array<int, array{question: string, answer: string}>}>
     */
    public function sections(): array
    {
        return [
            [
                'id' => 'basics',
                'title' => __('Product basics'),
                'blurb' => __('What KolleK is, who it is for, and the words the rest of this page uses.'),
                'items' => $this->pairs([
                    [
                        __('What is KolleK?'),
                        __('KolleK is an open source application for cataloguing collections. You record what you own, item by item and physical copy by physical copy, with photos, documents, values, and history, and you either run it on your own server or use an instance somebody else runs. It is released under the MIT licence.'),
                    ],
                    [
                        __('Who is KolleK for?'),
                        __('Private collectors who have outgrown a spreadsheet, first. Households, clubs and small organisations work too, because an account can hold several people with different roles. It is not institutional collection management software, and it does not try to be.'),
                    ],
                    [
                        __('What kinds of collections can I manage?'),
                        __('Any kind. A new account starts with twelve ready made collection types (comics, trading cards, vinyl records, CDs, DVDs, coins, stamps, books, action figures, video games, watches and wine), and you can edit any of them or write your own from scratch.'),
                    ],
                    [
                        __('Can I manage more than one collection?'),
                        __('Yes, and nothing in the code limits how many. Each collection has its own name, emoji, description, currency, and its own categories and sets.'),
                    ],
                    [
                        __('What is the difference between an item and a copy?'),
                        __('An item is the thing in the abstract: a title, a model, an edition. A copy is one physical object you actually own of that item, with its own condition, location, price paid, and history. Three of the same comic is one item with three copies. It is the single most important idea in KolleK.'),
                    ],
                    [
                        __('Can KolleK handle both simple lists and detailed catalogues?'),
                        __('Yes. An item can be a name and a photo, or a record with custom fields, several copies, valuations, insurance, loans, maintenance, provenance and attached documents. The fields you never define never appear.'),
                    ],
                    [
                        __('Is KolleK available in multiple languages?'),
                        __('Yes, in seven: English, French, Spanish, German, Brazilian Portuguese, Simplified Chinese and Japanese. Each person picks their own language, and the documentation and this public site are translated as well.'),
                    ],
                    [
                        __('Is there a mobile app?'),
                        __('No. There is no native app for iOS or Android, and none is in the codebase today. KolleK is a web application you open in a browser.'),
                    ],
                    [
                        __('Can I use KolleK on a phone or tablet?'),
                        __('Yes. Every screen is laid out down to phone width, and adding photos uses your device\'s normal file picker, which on a phone includes the camera.'),
                    ],
                    [
                        __('Is KolleK open source?'),
                        __('Yes, under the MIT licence, with the full application source public on GitHub. That licence cannot be revoked for the code that is already published.'),
                    ],
                ]),
            ],
            [
                'id' => 'hosting',
                'title' => __('Hosting and ownership'),
                'blurb' => __('The two ways to run KolleK, and what happens to your data in each.'),
                'items' => $this->pairs([
                    [
                        __('Can I self-host KolleK?'),
                        __('Yes, and it is the main supported way to run it. A Docker Compose stack starts the web server, a queue worker, a scheduler and the database, and nothing is held back from the self-hosted version.'),
                    ],
                    [
                        __('What is the difference between hosted KolleK and self-hosted KolleK?'),
                        __('The software is the same, and so is the feature set: nothing is locked behind a plan, and no feature is held back from the self-hosted version. What differs is who runs the server, applies the updates and keeps the backups, and that a hosted account holds ten items for free before it has to be unlocked. A self-hosted instance has no such limit.'),
                    ],
                    [
                        __('Which option do you recommend?'),
                        __('Self-host, if you are comfortable running a small Docker stack and taking responsibility for its backups. It is free, and your data never leaves your machine. If you would rather not run anything, use a hosted instance.'),
                    ],
                    [
                        __('Do I own my data?'),
                        __('Yes. You keep ownership of everything you add, and the terms of use say so. On the hosted service we store and process it to run the service, nothing else, and we do not train anything on it.'),
                    ],
                    [
                        __('Can I export my data?'),
                        __('Partly, and this is the honest gap in the product today. You can export a collection type definition as JSON and the list of open loans as CSV. There is no item level or whole collection export yet. If you self-host, the complete answer is a backup of the database and the storage volume, which holds everything.'),
                    ],
                    [
                        __('Can I move from hosted KolleK to self-hosted KolleK?'),
                        __('Not through the app yet, because item export does not exist. Until it does, moving means asking the operator of your hosted instance for a database and file dump and restoring it into your own installation.'),
                    ],
                    [
                        __('Can I move from self-hosted KolleK to hosted KolleK?'),
                        __('Same answer in the other direction: there is no import path for items yet. Two instances can be merged only at the database level, by whoever operates them.'),
                    ],
                    [
                        __('What happens if I stop using KolleK?'),
                        __('A self-hosted instance keeps running exactly as it is. It calls home to nothing, needs no licence check, and the MIT licence means nobody can take the code away. On a hosted instance, you can delete your account and everything in it whenever you like.'),
                    ],
                    [
                        __('Where is hosted KolleK run?'),
                        __('On Laravel Cloud, with the database on Turso and Cloudflare in front for DNS, caching and protection. Depending on those providers, your information may be processed in a country other than your own, and we do not currently offer a choice of region.'),
                    ],
                    [
                        __('What infrastructure providers do you use?'),
                        __('Laravel Cloud for the application, Turso for the database, and Cloudflare for DNS, routing, TLS and abuse protection. They process data on our behalf as infrastructure providers, and the privacy policy lists them by name.'),
                    ],
                ]),
            ],
            [
                'id' => 'pricing',
                'title' => __('Pricing and plans'),
                'blurb' => __('What the software costs, and what is honestly not built yet.'),
                'items' => $this->pairs([
                    [
                        __('How much does KolleK cost?'),
                        __('Self-hosting is free, and costs you whatever your own server costs. On the hosted service an account holds ten items for free, and unlocking it for good is a single payment of $49. Checkout is not open yet, so nothing can be bought today, but the screens that explain the limit are already in the application.'),
                    ],
                    [
                        __('Is there a monthly subscription?'),
                        __('No, and there is no mechanism for one. Nothing in KolleK can charge a card on a schedule. The commitment is one payment that unlocks an account permanently, never a rental.'),
                    ],
                    [
                        __('What does the one-time payment include?'),
                        __('It removes the item limit on a hosted account, and pays for the operational side of running one: the servers, the updates, the backups and the support. It does not unlock features, because every feature is already in the application however you run it.'),
                    ],
                    [
                        __('Is self-hosting really free?'),
                        __('Yes. There is no trial, no reduced tier, and no licence key to buy. The MIT licence on the published code cannot be withdrawn later.'),
                    ],
                    [
                        __('Are there limits on items, collections, members, photos, or documents?'),
                        __('A self-hosted instance has no quotas at all. On the hosted service the only quota is on items: an account holds ten for free, accepts five more as a grace, and then stops accepting new ones until it is unlocked. Nothing already added is ever deleted, hidden or made read-only. Collections, members, photos and documents are not counted either way. The remaining limits are per file (10 MB for a photo, 12 MB for a document) and whatever disk you give the instance.'),
                    ],
                    [
                        __('Do you offer refunds?'),
                        __('No, and we say so before you pay rather than in the small print. A reversal costs a project this size the payment fee, the chargeback penalty and an afternoon of paperwork. If anything goes wrong, email us and we will fix it. If you would rather not pay at all, self-hosting is free and always will be.'),
                    ],
                    [
                        __('Will pricing change later?'),
                        __('It may, for future purchases. The terms of use say a completed one-time purchase is not repriced retroactively and does not turn into a subscription.'),
                    ],
                    [
                        __('Do I need a credit card to self-host?'),
                        __('No. Self-hosting involves no account with us, no sign-up and no payment step. You clone the repository, or pull the image, and run it.'),
                    ],
                    [
                        __('Are taxes included?'),
                        __('When hosted plans are sold, VAT and sales tax depend on where you are and are shown at checkout before you pay, as the terms of use describe.'),
                    ],
                    [
                        __('Do you offer team, museum, or organization pricing?'),
                        __('There is no per-seat charge and no separate tier, so a household and a thirty person organisation would pay the same. If you need an invoice or procurement paperwork, get in touch.'),
                    ],
                ]),
            ],
            [
                'id' => 'privacy',
                'title' => __('Privacy and security'),
                'blurb' => __('What is protected, who holds the keys, and where the protection stops.'),
                'items' => $this->pairs([
                    [
                        __('Is my collection private?'),
                        __('Nothing you record is visible outside your account: there is no public link and no discovery of any kind. Inside an account, be aware that every member can currently browse every collection in it. The per-collection visibility setting is saved but not enforced yet.'),
                    ],
                    [
                        __('Do you sell personal information?'),
                        __('No. The privacy policy states it plainly, and there is no advertising model here that would need it.'),
                    ],
                    [
                        __('Do you track users for advertising?'),
                        __('No. There are no advertising trackers, no third-party analytics and no pixels in the application. Operational logs (requests, errors, security events) are kept to run and debug the service.'),
                    ],
                    [
                        __('Is data encrypted at rest?'),
                        __('Yes. Names, descriptions, identifiers and notes are encrypted by the application with your instance\'s key before they reach the database, and traffic is over TLS. It is not end-to-end encryption: whoever runs the instance holds the key and can read the data.'),
                    ],
                    [
                        __('Who controls the encryption keys?'),
                        __('If you self-host, you do. The key is the APP_KEY in your own environment file, and nobody else ever sees it. On the hosted service, we hold the application key and infrastructure providers do not receive it.'),
                    ],
                    [
                        __('Do Laravel Cloud, Turso, or Cloudflare have access to my data?'),
                        __('They operate the systems that store and carry it, as infrastructure providers acting on our behalf. They are not given the application encryption key, and they get no right to use your collection for anything of their own.'),
                    ],
                    [
                        __('Why do you use Cloudflare?'),
                        __('For DNS, TLS termination, caching and protection against denial of service and other abuse. It keeps a small project fast and reachable without a team of infrastructure engineers behind it.'),
                    ],
                    [
                        __('What security features does KolleK support?'),
                        __('Two-factor authentication with recovery codes, passwordless magic link sign-in, passwords checked against known breaches, alert emails for sensitive events (a new device, a new IP address, failed sign-ins, an API key created or revoked), role based permissions on every write, revocable API keys, and an audit trail of who changed what. There is no single sign-on, no hardware key support, and no screen for managing active sessions.'),
                    ],
                    [
                        __('Does KolleK support two-factor authentication?'),
                        __('Yes, with any standard authenticator app, and a set of one-time recovery codes issued when you turn it on.'),
                    ],
                    [
                        __('Are backups encrypted?'),
                        __('The sensitive fields are encrypted by the application before they are written, so they are still encrypted inside any database dump, including yours. Beyond that, hosted backups are kept for operational recovery only, and self-hosted backups are protected by whatever you protect them with.'),
                    ],
                    [
                        __('What happens if there is a security issue?'),
                        __('Report it privately by email to the maintainer rather than in a public issue, and it gets looked at before anything else. There is no bug bounty, and no formal disclosure timetable to promise you.'),
                    ],
                    [
                        __('Can I delete my account and data?'),
                        __('Yes. You can delete your own user from your profile, and an owner can delete the whole account with everything in it. There is also an option to have your user deleted automatically after a long stretch of inactivity. Deletion works its way out of backups on their normal rotation.'),
                    ],
                ]),
            ],
            [
                'id' => 'data',
                'title' => __('Data, backups, and portability'),
                'blurb' => __('What goes in, what comes back out, and who is responsible for keeping it.'),
                'items' => $this->pairs([
                    [
                        __('What data can I store in KolleK?'),
                        __('Collections, items and the copies you own, photos, documents, categories, tags, locations, conditions, sets and series, and, on each copy, transactions, valuations, insurance records, loans, maintenance records, provenance events and location history.'),
                    ],
                    [
                        __('Can I upload photos?'),
                        __('Yes, several per item, in any order, with one marked as the main one. Each upload is resized into smaller variants for browsing, and a photo can be up to 10 MB. The account also has a photo library that shows every image in one place.'),
                    ],
                    [
                        __('Can I attach documents?'),
                        __('Yes. A document attaches to a copy or to one of its records (a transaction, a valuation, an insurance record, a loan, a maintenance record, a provenance event). PDFs, images, Word and Excel files, CSV and plain text are accepted up to 12 MB, and you can also record a document that lives at an external URL.'),
                    ],
                    [
                        __('Can I record valuations, receipts, or insurance details?'),
                        __('Yes. Valuations are a history rather than one number, each with a type and a confidence level, so you can see how a copy moved over time. Purchases and sales are transactions, insurance is its own record with policy details, and the paperwork attaches to any of them.'),
                    ],
                    [
                        __('Can I export my collection?'),
                        __('Not yet. Exporting is limited to collection type definitions as JSON and open loans as CSV. Item and whole collection export is a known gap, tracked on the feature status page in the documentation.'),
                    ],
                    [
                        __('Can I import existing data?'),
                        __('Only a collection type definition, from the JSON that the export produces. There is no spreadsheet or CSV import for items, so today a collection is entered by hand or through the API.'),
                    ],
                    [
                        __('Do you provide backups?'),
                        __('The hosted service keeps backups for operational recovery. They exist so the service can be restored, not as a self-service undo, and they are not a substitute for your own records.'),
                    ],
                    [
                        __('Are backups included for hosted accounts?'),
                        __('Yes, with no add-on to buy. They are operational backups of the whole instance rather than per-account snapshots you can browse.'),
                    ],
                    [
                        __('Who is responsible for backups when self-hosting?'),
                        __('You are, entirely. Everything lives in two places: the database volume and the storage volume that holds photos and documents. Back both up with whatever tooling you already trust, and keep a copy of your APP_KEY, because the encrypted data is unreadable without it.'),
                    ],
                    [
                        __('Can I recover deleted items?'),
                        __('Collections, items, copies, categories and sets go to the trash and can be restored for 30 days by default, after which a daily job removes them for good. Photos, documents and history records are deleted immediately and cannot be recovered from inside the app.'),
                    ],
                    [
                        __('Can I delete data permanently?'),
                        __('Yes. Emptying the trash removes what is in it straight away, and deleting the account removes everything belonging to it.'),
                    ],
                ]),
            ],
            [
                'id' => 'features',
                'title' => __('Features'),
                'blurb' => __('What the application actually does once your first collection exists.'),
                'items' => $this->pairs([
                    [
                        __('Can I create custom collection types?'),
                        __('Yes. A type describes what a kind of collection holds, and every collection using it inherits those fields. Edit the ones that ship with a new account, or build your own, and export the definition as JSON to reuse it elsewhere.'),
                    ],
                    [
                        __('Can I add custom fields?'),
                        __('Yes, in six kinds: text, number, date, yes/no, a select list of your own options, and a rating out of five. Fields sit in named groups so a long form stays readable, and both fields and groups can be reordered.'),
                    ],
                    [
                        __('Can I organize items by category, tag, location, set, or series?'),
                        __('All five, and they are independent. Categories nest inside a collection, locations nest as deep as you like (a shelf in a box in a room), tags are shared across the whole account, a set tracks completion against a target count, and a series groups items that belong together.'),
                    ],
                    [
                        __('Can I track multiple copies of the same item?'),
                        __('Yes, and that is the point of separating items from copies. Each copy carries its own identifier, condition, location, acquisition date, price paid and history.'),
                    ],
                    [
                        __('Can I track loans or custody?'),
                        __('Yes, in both directions: what you have lent out and what you have borrowed in. A loan records the other party, the dates, the due date and the condition the copy left and came back in, and the open loans of either direction download as CSV.'),
                    ],
                    [
                        __('Can I record provenance or ownership history?'),
                        __('Yes. Provenance events form a dated chain on a copy, each with its own type, the party involved, a description and any documents you attach.'),
                    ],
                    [
                        __('Can I track maintenance and repairs?'),
                        __('Yes. Maintenance records carry the kind of work, the date, the cost, who did it and the paperwork that came back with it.'),
                    ],
                    [
                        __('Can I track valuations and transactions?'),
                        __('Yes. Valuations record what a copy is worth over time and where that figure came from. Transactions record what was paid or received, when, and with whom. Amounts are stored in the currency of the collection.'),
                    ],
                    [
                        __('Can I manage documents and photos?'),
                        __('Yes. Photos are managed on the item and from an account-wide library that shows every image you have uploaded. Documents are managed on the copy or record they belong to, and every one of them turns up in search.'),
                    ],
                    [
                        __('Can I search across my whole account?'),
                        __('Yes. One search covers items, copies, collections, photos, loans, locations, sets, series, categories, tags and documents, and results are grouped by kind. It works even though the underlying text is encrypted, because words are indexed as hashes rather than stored in the clear.'),
                    ],
                    [
                        __('Can I view collection statistics?'),
                        __('Yes, per collection: totals, value over time, acquisitions per month, breakdowns by category, condition and location, the most valuable items, and how far your sets are from complete. There is no account-wide roll-up screen yet, and the dashboard shows recent activity instead.'),
                    ],
                ]),
            ],
            [
                'id' => 'collaboration',
                'title' => __('Collaboration and accounts'),
                'blurb' => __('Bringing other people in without handing over the whole account.'),
                'items' => $this->pairs([
                    [
                        __('Can I invite other people?'),
                        __('Yes. An owner invites by email and picks the role at that moment. The invitation is claimed with a link, and there is no charge per member.'),
                    ],
                    [
                        __('What roles and permissions are available?'),
                        __('Three: owner, editor and viewer. Owners manage members, settings and deletion, editors create and change records, viewers only read. An account always keeps at least one owner, so the last one cannot be removed or demoted.'),
                    ],
                    [
                        __('Can multiple people work on the same collection?'),
                        __('Yes. Everyone in an account works on the same collections, and every action is logged with who did it, so the activity feed and an item\'s history show where a change came from.'),
                    ],
                    [
                        __('Can I share a collection publicly?'),
                        __('Not yet. A collection carries a visibility setting, and it is saved, but nothing enforces it and there is no public link, so a collection marked public is not reachable from outside the account at all.'),
                    ],
                    [
                        __('Can I keep some collections private?'),
                        __('Not from other members of your own account, for the same reason: visibility is recorded but not enforced today. Anyone you invite can see every collection in the account, so keep genuinely separate things in separate accounts for now.'),
                    ],
                    [
                        __('Can I remove a member later?'),
                        __('Yes, immediately, by an owner. Access ends there and then, and what the person recorded stays in the account.'),
                    ],
                    [
                        __('Can organizations or households use KolleK?'),
                        __('Yes, that is what accounts and roles are for. One thing to plan around: a person belongs to exactly one account, and an email address can only have one user, so somebody cannot join a club account and keep a personal one.'),
                    ],
                    [
                        __('Can I manage support conversations inside the app?'),
                        __('The application has a support section where you open a thread and get replies in place. It is switched off unless the instance operator turns it on, so on a self-hosted instance it is normally not there.'),
                    ],
                ]),
            ],
            [
                'id' => 'self-hosting',
                'title' => __('Self-hosting'),
                'blurb' => __('Running your own instance: what it needs, how it updates, where the responsibility sits.'),
                'items' => $this->pairs([
                    [
                        __('What do I need to self-host KolleK?'),
                        __('Docker Engine 24 or newer with the Compose plugin, and enough disk for your photos and documents. A small VPS or a machine at home is plenty. The stack starts the web server, a queue worker, a scheduler and MySQL.'),
                    ],
                    [
                        __('Is Docker supported?'),
                        __('Yes, and it is the recommended path. Copy the example environment file, generate an application key, and bring the stack up. The full guide lives in the repository and in the self-hosting section of the documentation.'),
                    ],
                    [
                        __('Can I run KolleK without Docker?'),
                        __('Yes. It is a conventional Laravel application, so PHP 8.4 with the usual extensions, a database, a web server, a queue worker and a scheduler will do. You just get less help from us than the documented Docker path.'),
                    ],
                    [
                        __('What database does self-hosted KolleK use?'),
                        __('MySQL 8, which the Docker stack runs for you. SQLite works for local development, and other engines Laravel supports may work, but MySQL is what the project is tested and documented against.'),
                    ],
                    [
                        __('How do updates work?'),
                        __('Pull the newer image and restart the stack. Migrations run on boot, and they are written to be safe on an existing database, so your data and uploads are never reset by an upgrade.'),
                    ],
                    [
                        __('How do backups work when self-hosting?'),
                        __('They are yours to arrange. Back up the database volume and the storage volume, keep your APP_KEY somewhere safe, and test a restore occasionally. Nothing about backups happens automatically on your own instance.'),
                    ],
                    [
                        __('Can I use my own storage provider?'),
                        __('Yes. Photos and documents go to the local volume by default, and pointing FILESYSTEM_DISK at any S3 compatible bucket moves them there. Files are still served through account-checked routes rather than public URLs.'),
                    ],
                    [
                        __('Can I use my own email provider?'),
                        __('Yes, through the mail settings in your environment file: any SMTP server, or Resend. Email defaults to the log file, so set this up before you invite anybody.'),
                    ],
                    [
                        __('Can I customize the app?'),
                        __('The MIT licence allows any change you like. The practical caveat is that a heavily patched instance is harder to upgrade, so contributing a change upstream is usually the better trade.'),
                    ],
                    [
                        __('Do you provide support for self-hosted installations?'),
                        __('Community support, through GitHub issues and discussions. There is no paid support contract to buy, and that is the honest difference between running it yourself and having somebody run it for you.'),
                    ],
                ]),
            ],
            [
                'id' => 'api',
                'title' => __('API and developers'),
                'blurb' => __('Programmatic access, and how to help if you want to.'),
                'items' => $this->pairs([
                    [
                        __('Does KolleK have an API?'),
                        __('Yes, a JSON API that mirrors the web application: the same actions and the same rules, roughly a hundred and fifty endpoints, with the reference published at /docs/api on every instance.'),
                    ],
                    [
                        __('How do I authenticate with the API?'),
                        __('With a personal access token sent as a bearer token in the Authorization header, over HTTPS. You can also exchange an email and password for a token through the login endpoint.'),
                    ],
                    [
                        __('Can I create API keys?'),
                        __('Yes, from your profile settings, and you can revoke any of them individually. A key carries exactly the permissions your role has, and it has no scopes or expiry date today. You get an email whenever a key is created or destroyed.'),
                    ],
                    [
                        __('What can I access through the API?'),
                        __('Collections and their types, custom fields and groups, items, copies, photos, tags, categories, sets, series, locations, conditions, documents, transactions, valuations, insurance, maintenance, provenance, location history, loans, the trash, search, statistics, members and invitations. Reads and writes, subject to your role.'),
                    ],
                    [
                        __('Are there rate limits?'),
                        __('Yes: 60 requests a minute once authenticated, and 6 a minute on sign-in and registration to slow down guessing.'),
                    ],
                    [
                        __('Are webhooks supported?'),
                        __('Halfway, and it is worth knowing which half. You can register endpoints and each gets its own signing secret, but no application event fires a webhook yet. The delivery machinery is ready and waiting for events to be wired up.'),
                    ],
                    [
                        __('Can I build scripts or integrations?'),
                        __('Yes. Anything the interface does, the API does, so importing a catalogue from a script or exporting one on a schedule is entirely possible today.'),
                    ],
                    [
                        __('Is the API documentation generated from the codebase?'),
                        __('It is written next to the code, in the repository, and rendered into the reference you read at /docs/api, with a Markdown version of every section. A test fails the build when an endpoint has no documentation, which is what keeps the two together.'),
                    ],
                    [
                        __('Can I contribute to KolleK?'),
                        __('Please do. Bug reports, feature ideas, translations, documentation and pull requests are all welcome, and issues labelled "good first issue" are a friendly place to start.'),
                    ],
                    [
                        __('Where can I report bugs or request features?'),
                        __('In the GitHub issues for the project, or in the discussions if it is more of a conversation than a bug. Security problems are the exception: email those privately instead.'),
                    ],
                ]),
            ],
            [
                'id' => 'trust',
                'title' => __('Trust and limitations'),
                'blurb' => __('What KolleK deliberately does not do. Worth reading before you rely on it.'),
                'items' => $this->pairs([
                    [
                        __('Does KolleK verify item authenticity?'),
                        __('No. It records what you tell it. It has no way to confirm that an item is genuine, and it never claims to.'),
                    ],
                    [
                        __('Is KolleK an appraisal tool?'),
                        __('No. It stores the valuations you or a professional enter, with a note of how confident that figure is. It does not look up market prices or generate a value of its own.'),
                    ],
                    [
                        __('Does KolleK provide legal, tax, insurance, or financial advice?'),
                        __('No. It is a record-keeping tool, and the terms of use say as much. Decisions about coverage, tax and sale belong with the relevant professional.'),
                    ],
                    [
                        __('Can KolleK prove ownership?'),
                        __('No. A well kept record with receipts, photos and provenance is useful supporting evidence, but it is not proof of title, and anybody with access to the account can write anything into it.'),
                    ],
                    [
                        __('Can KolleK replace professional inventory, insurance, or museum software?'),
                        __('For a private collector or a small group, often yes. For accession standards, conservation workflows or statutory reporting, no, and you should not plan around it doing so.'),
                    ],
                    [
                        __('What happens if I enter incorrect information?'),
                        __('It is stored faithfully, mistake and all. Each item keeps a history of what changed and who changed it, so you can find the error and correct it.'),
                    ],
                    [
                        __('What are the current limitations?'),
                        __('The honest list: no item or collection export and import, per-collection visibility that is saved but not enforced, webhooks that never fire, no native mobile app, no single sign-on or session management, statistics per collection rather than for the whole account, and one account per person. The feature status page in the documentation keeps this list current.'),
                    ],
                    [
                        __('Where can I see the roadmap?'),
                        __('There is no dated roadmap to point at, on purpose. The open issues on GitHub show what is being worked on, and the feature status page in the documentation says what is finished and what is not.'),
                    ],
                    [
                        __('How do I contact support?'),
                        __('Through the support section inside the app where an instance has it switched on. Otherwise GitHub issues and discussions are the place, and one person reads them.'),
                    ],
                    [
                        __('How do I report abuse or security concerns?'),
                        __('Email the maintainer privately rather than opening a public issue, so a vulnerability can be fixed before it is described in public. Reports made in good faith are welcome and are dealt with first.'),
                    ],
                ]),
            ],
        ];
    }

    /**
     * How many questions the page holds, used in the introduction.
     */
    public function totalQuestions(): int
    {
        return array_sum(array_map(
            fn (array $section): int => count($section['items']),
            $this->sections(),
        ));
    }

    /**
     * Turn the [question, answer] tuples above into named keys, so the view
     * never indexes into an array by number.
     *
     * @param  array<int, array{0: string, 1: string}>  $pairs
     * @return array<int, array{question: string, answer: string}>
     */
    private function pairs(array $pairs): array
    {
        return array_map(
            fn (array $pair): array => ['question' => $pair[0], 'answer' => $pair[1]],
            $pairs,
        );
    }
}
