---
id: search.query
kicker: Search
title: How search works
doc: search.overview
---
Every word you type has to match somewhere in a record, so adding a word narrows the results rather than widening them. A word matches from its start, which means "spi" finds "Spider-Man".

Case and punctuation are ignored, so "asm-300", "asm 300" and "ASM_300" behave the same. Single letters are never indexed on their own, so a query made only of them returns nothing rather than everything.
