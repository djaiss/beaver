# CLAUDE.md

LifeOS is a personal CRM.

## Instructions

- Write code as simply as possible - do not over-engineer so anyone can understand it.
- Always follow the Laravel best practices and how we structure our codebase.
- If you are unsure about a specific implementation, ask for clarification before proceeding.
- Document important decisions you take regarding the project in `.claude/decision.md` file. Keep it short and concise, but provide enough context for future reference.

## Tech Stack & Architecture
- Backend: PHP 8.4+ / latest version of Laravel
- Frontend: Blade / Tailwind CSS / Alpine Ajax / Alpine.js
- Data is encrypted at rest in the database using Laravel's built-in encryption.

## Data hierarchy

- Any user can create an account.
- A user can have vaults.
- A vault can have multiple users with different roles.
