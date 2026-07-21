# Scheduled maintenance jobs

Every night, your instance tidies up after itself. This page tells you what runs, when, and what has to be true for it to happen, so that nothing the app does on its own ever surprises you.

## The nightly jobs

Three jobs run daily, each queued on the low priority queue:

- **00:30, inactive user deletion.** Deletes users who have personally opted into [automatic deletion after inactivity](../data-safety/inactive-user-deletion.md) and have been inactive for six months or more. Each deletion is reported to the address in `ACCOUNT_DELETION_NOTIFICATION_EMAIL`. Users who never opted in are never touched.
- **01:00, trash purge.** Permanently deletes anything in the [trash](../data-safety/restore-from-trash.md) older than the retention period (`TRASH_RETENTION_DAYS`, 30 days by default). Inside the window, trashed objects remain restorable.
- **02:00, overdue loan flagging.** Marks active [loans](../copy-history/lend-and-borrow-copies.md) whose due date has passed as overdue, so collectors see at a glance what has not come back.

All three are safe and expected. They only ever act on things users have explicitly deleted, opted into, or dated.

## What must be running

Two containers make this happen:

- The **scheduler** role decides that it is time and queues each job.
- The **queue** role actually executes them.

:::note
If either container is down, maintenance silently stops: trash accumulates past its retention date, overdue loans stay marked active, and opted in inactive users are not cleaned up. Nothing breaks, but nothing runs. Check `docker compose ps` if nightly behavior seems to have stopped.
:::

Everything catches up on the next successful run; a missed night is not a problem.

## Where to next

- Adjust the retention window in [Configure your instance](configure-your-instance.md).
- See what users experience on the other side in [Restore something from the trash](../data-safety/restore-from-trash.md).
