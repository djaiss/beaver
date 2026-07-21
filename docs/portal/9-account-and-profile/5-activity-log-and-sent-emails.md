---
id: activity.logAndSentEmails
title: Your activity log and sent emails
slug: activity-log-and-sent-emails
section: account-and-profile
---

# Your activity log and sent emails

KolleK keeps two records about you that you can consult at any time: everything you have done, and every email the system has sent you. Both live in your profile area, and both exist for the same reason, transparency. When you wonder "did I really change that" or "did that magic link email actually send", the answer is here.

## Your activity log

The [activity trail](../4-core-features/8-activity-feed-and-audit-trail.md) that runs through the whole account has a personal view: a full history of your own actions, from creating an item to changing a setting. Open it from your profile area.

Use it to retrace your steps. If a copy's location looks wrong, your log will show whether you moved it, and when.

## Your sent emails

KolleK records every email it sends you: magic links, invitations you received, verification messages, and [security alerts](../10-security/6-security-alert-emails.md). Your profile area lists them, most recent first, ten per page.

Each entry shows what was sent and when. Where the instance's mail service reports back, you will also see whether the message was delivered, or whether it bounced.

This list is the fastest way to troubleshoot missing email:

- **The email appears here but never reached your inbox.** Check your spam folder, and check whether the entry shows a bounce.
- **The email does not appear here at all.** The action that should have triggered it did not happen, so request it again.
- **Emails appear here but none are ever delivered.** On a self hosted instance this usually means mail delivery is not configured yet. Point your operator at [set up email delivery](../14-self-hosting/4-set-up-email-delivery.md).

:::note
This page shows emails sent to you. It is personal, like the rest of your profile, and other members cannot browse your list.
:::

## Where to next

- Understand the account wide history in [The activity feed and audit trail](../4-core-features/8-activity-feed-and-audit-trail.md).
- Missing an expected email? Work through [Troubleshooting email delivery](../15-troubleshooting/3-troubleshoot-email-delivery.md).
