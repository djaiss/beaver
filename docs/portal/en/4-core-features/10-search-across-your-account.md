---
id: search.overview
title: Search across your account
slug: search-across-your-account
section: core-features
---

# Search across your account

Emma collects comics, vinyl and trading cards. She has three collections, a few hundred items and twice as many copies. Somebody asks her whether she still owns that Spider-Man issue with the black cover. She should not have to remember which collection it lives in.

Search answers that question from one box. Type a word, and KolleK looks across everything your account holds at once: items, collections, copies, photos, loans, locations, sets, series, categories, tags and documents.

Open it from **Search** at the top of the sidebar, or press **⌘K** (**Ctrl K** on Windows and Linux) from anywhere in the app.

## What gets searched

Search does not only look at names. It also looks at the words filed around a record, which is what makes it useful rather than merely literal.

Searching for `spider` finds:

- an item called **Amazing Spider-Man #300**
- the copy **ASM-300-B**, because its item is called that
- a photo named `spider-man-300-front.jpg`
- an item you never named "Spider-Man" at all, because you gave it the tag **spider-man**
- a loan to a friend, because the copy on loan is that comic

So a search that starts from a tag, a category or a location still lands you on the thing you were actually looking for.

:::note
Anything in the trash is left out of search. @doc(dataSafety.restoreFromTrash, "Restore it") and it becomes findable again.
:::

## How matching works

Four rules cover everything.

**Every word has to match.** Adding a word narrows the result rather than widening it. `miles davis` finds only the records where both words appear somewhere. If you get too much back, add a word.

**A word matches from its start.** Typing `spi` finds **Spider-Man**. You never have to finish a word, but you do have to begin it: searching `man` will not find **Spider-Man**, because `man` is not the start of any word in it.

**Case and punctuation are ignored.** `asm-300`, `asm 300` and `ASM_300` all behave the same way, which matters when your own identifiers use dashes, dots or underscores and you cannot remember which.

**Single letters are ignored.** A letter on its own is too common to be worth indexing, so it is dropped from your query. If you search for a single letter and nothing else, you get nothing back rather than everything.

## Reading the results

Results are grouped by what they are, with items first. Each row shows the name, a badge saying what kind of record it is, the collection it belongs to when it has one, and a line of context: how many copies an item has, where a copy is kept, who a loan went to.

On the right of each row, **Name match** means every word you typed appeared in the name of the record itself. **Text match** means at least one word was found somewhere further out, such as a description or a tag. Name matches are ranked first, so the closest answer is usually the first row.

The chips above the results let you narrow to one kind of record. Each has its own address, so a search for items only can be bookmarked or shared with a colleague on the same account.

At most 50 results are shown. When there are more, the count under the list tells you how many matched in total, and adding a word to your query is the fastest way to get to the one you want.

## Who can search what

Search is scoped to your account. You never see anything belonging to another account, and nobody outside your account sees yours.

Inside your account, every role can search, and every result opens a screen that role is allowed to open. @doc(accounts.usersAndRoles, "Viewers") are one exception: tags are managed on a screen only owners and editors have, so a viewer does not get standalone tag results. A viewer still finds everything *tagged* `spider-man`, because tag names count towards the items that carry them.

## If something is missing

Search reads an index that is kept up to date as you work, so a new item is findable the moment you save it.

Two cases can leave it briefly behind:

**You renamed something other records mention.** Renaming a collection means every item in it has to be re-indexed under the new name. That happens in the background, so give it a moment.

**You have just upgraded a self-hosted instance.** The index starts empty on an existing installation and has to be built once. Run @doc(selfHosting.cliCommands, "the rebuild command") and everything becomes findable:

```bash
php artisan search:rebuild-index
```

That command is safe to run again at any time, so it is also the fix if the index ever drifts.

## Where to next

- @doc(items.tagAndFind) covers tagging, which is what makes search find things you did not name literally.
- @doc(organizing.categoriesSetsAndSeries) explains the categories, sets and series that also feed search.
- @doc(photos.library) has a search of its own, for when you are looking through photos rather than across the account.
