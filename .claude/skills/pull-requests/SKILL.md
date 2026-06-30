---
name: pull-requests
description: Add a new locale to the application. Use when the user wants to support an additional language, register a locale in config, and generate its lang/*.json translation file. Trigger whenever adding a language, new locale, or i18n support is mentioned.
---

### Create a pull request

- pull request title should follow Conventional Commits naming convention, e.g. `feat: add new locale fr_FR`.
- try to avoid uppercase in the title, except for proper nouns and acronyms.
- pull request description should include
    - if it's a new feature: 
        - summarize the changes made in the PR in indicative mood (present tense), using a list if necessary.
    - if it's a change or fix: 
        - explain what the situation was.
        - summarize the changes made in the PR in indicative mood (present tense), using a list if necessary.
    - add notes about important details, if necessary.
- indicate if this PR will close an issue, and reference the issue number.
- do not add mention of claude code anywhere.
