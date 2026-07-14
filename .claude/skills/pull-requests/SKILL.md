---
name: pull-requests
description: Conventions for writing pull requests, including Conventional Commits titles and structured descriptions. Use when the user wants to create or open a pull request, or write a PR title or description. Trigger whenever a pull request, PR, or merge request is mentioned.
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
