# Grant instance administrator access

An instance administrator is the person who looks after the server itself, with a panel that sees across every account on the instance. This page explains what the flag is, how to grant it, and the safeguards around it.

## What the flag is, and is not

The instance administrator flag is server wide and completely separate from [account roles](../core-concepts/accounts-users-and-roles.md). It grants exactly one thing: access to the [instance administration panel](instance-administration-panel.md).

- It gives no extra powers inside the administrator's own account. An instance administrator who is a viewer in their account still cannot edit items there.
- It is per user, not per account. Grant it to the specific person operating the server, typically yourself.

Alex, who operates the club's instance, holds the flag on their own user and is an ordinary owner inside their own account. The two facts are unrelated.

## Grant and revoke

The flag is managed from the command line, which is deliberate: bootstrap access to the server wide panel should require access to the server.

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

Revoke it the same way:

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com --revoke
```

An existing administrator can also toggle the flag on other users from inside the panel.

## Why the panel pretends not to exist

To anyone without the flag, `/instance-admin` answers **404 Not Found**, not "access denied". The panel does not announce its existence to people who cannot use it, so probing an instance reveals nothing. If you granted yourself the flag and still see a 404, check you are signed in as the exact user you granted it to.

## The lockout safeguards

Two rules protect the instance from losing its administrator:

- An administrator cannot revoke their own flag from the panel.
- An administrator cannot delete their own user from the panel.

So the panel can never be used to lock everyone out of the panel. And even if every administrator were gone, the command line path above always works, because it only requires access to the server.

## Where to next

- See what the panel can do in [The instance administration panel](instance-administration-panel.md).
- Browse the other operator commands in [Administer with the command line](cli-commands.md).
