# Decisions

## API: vault membership (issue #60)

- Wired the existing `Api\Adminland\MemberController` into `routes/api.php`.
- Scope kept intentionally small: only **listing members**, **showing a member**, and **joining a vault**. Role changes and member removal are deliberately left out for now.
- `index`/`show` live under the `vault.api` middleware group (`vaults/{id}/members`), so they require the authenticated user to already be a member.
- **Join** (`POST vaults/join`) sits outside the `vault.api` group because the user is not a member yet; it reuses the `JoinVault` action and validates the `invitation_code` exactly like the web `JoinVaultController`.
- The `vault.api` middleware returns `403` (not `404`) for an authenticated non-member hitting a vault they don't belong to; tests assert that behaviour.
