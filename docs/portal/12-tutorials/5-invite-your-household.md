---
id: tutorials.inviteHousehold
title: "Tutorial: Invite your household or club and set permissions"
slug: invite-your-household
section: tutorials
---

# Tutorial: Invite your household or club and set permissions

A KolleK account is a shared workspace, and bringing people in safely is mostly a matter of choosing the right role for each person. In this tutorial you will invite two people at different roles, see what each can and cannot do, share one collection publicly while keeping another private, and adjust a role after the fact.

We will follow Emma, who catalogues comics with her partner Sam and likes showing her collection to her friend Leo. Sam helps with data entry, so he needs to edit. Leo only browses, so he should not be able to change anything.

Expect this to take about fifteen minutes, plus however long your invitees take to open their email.

## Before you start

- You must be an **owner** of the account. Only owners can invite people and change roles.
- Read [Accounts, users, and roles](../3-core-concepts/3-accounts-users-and-roles.md) if you have not. The one line version: viewers read, editors change catalogue content, owners also manage the account.
- Know your invitees' email addresses, and one thing about them: an invitation only works for an email that does not already have a KolleK account of its own, because a person belongs to exactly one account.

## Step 1: Invite Sam as an editor

::::steps
:::step title="Open the members area"
Go to your account's member settings, where members and pending invitations are listed.

::screenshot{label="Members screen with the invite form"}
:::

:::step title="Send the invitation"
Enter Sam's **email**, choose the **Editor** role, and send. Sam can now create and edit collections, items, and copies, but cannot invite people or touch account settings.
:::
::::

The invitation email contains a link that is valid for **seven days**. If it expires before Sam gets to it, simply invite him again.

## Step 2: Invite Leo as a viewer

Repeat the same steps for Leo, but leave the role as **Viewer**, which is the default. Leo will be able to browse everything in the account, including collections, items, and their histories, but every editing control will be out of his reach.

Choosing the smaller role is not unfriendly. It protects Leo too: he cannot accidentally delete an item or change a record while browsing.

## Step 3: What Sam and Leo experience

Each of them receives an email and opens the link. Since neither has a KolleK account yet, the page asks them to set their **first name**, **last name**, and a **password** (at least eight characters, and checked against known breaches). Then they land in Emma's account, already verified and signed in, with the role she chose.

If the link says an account already exists for that email, that person cannot join through this invitation. That situation and other invitation hiccups are covered in [Troubleshooting sign in](../15-troubleshooting/2-troubleshoot-sign-in.md).

## Step 4: Set each collection's visibility

Roles control the people inside the account. [Visibility](../3-core-concepts/12-visibility-and-sharing.md) records who each collection is meant for, from just you to anyone with a link.

Emma has two collections: "My Comics", which she wants to show the world one day, and "Wishlist Research", which is nobody's business but hers.

::::steps
:::step title="Mark one collection public"
On "My Comics", she sets the visibility to **Public**, marking it as the one she intends to share beyond the account.
:::

:::step title="Mark the other private"
"Wishlist Research" is set to **Private**, meant for her alone. **Shared**, the middle setting, marks a collection as meant for every account member.
:::
::::

:::note
Visibility is not enforced yet. Today Sam and Leo can still browse every collection in the account, including private ones, and there is no public link to send around, so nothing is visible outside the account at all. Setting visibility now means each collection behaves correctly the moment sharing arrives. See [Feature status and roadmap](../15-troubleshooting/5-feature-status.md).
:::

:::warning
When public links arrive, a public collection will be viewable by anyone who has the link, without signing in. Only mark a collection public if you are comfortable with every item in it being seen.
:::

The full walkthrough, including reverting, is in [Share a collection](../8-collaboration/5-share-a-collection.md).

## Step 5: Adjust a role later

A few weeks in, Leo starts spotting mistakes and wants to fix them himself. Emma opens the members screen, finds Leo, and changes his role from **Viewer** to **Editor**. The change applies immediately. Roles are a dial, not a life sentence, and moving someone down works the same way.

One safeguard to know about: an account must always keep at least one **owner**. KolleK will refuse to demote or remove the last owner, so a shared account can never end up ownerless and unmanageable.

:::warning
Removing a member deletes their user from the account and cannot be undone from the members screen. If someone only needs less access, change their role instead of removing them.
:::

## The result

Emma's account now has three people with three levels of trust: Emma owns and manages, Sam catalogues alongside her, and Leo browses and, lately, tidies. One collection is marked for the world, one for her alone, ready for the day sharing is enforced. Nothing about that setup is fixed; roles and visibility can change as the people do.

## Common mistakes to avoid

- **Inviting everyone as an editor by default.** Give the role the person needs today. Upgrading later is one click.
- **Assuming private already hides a collection.** Visibility is recorded but not enforced yet, so every member can browse every collection today, private or not. Keep truly personal catalogues in an account of your own for now.
- **Removing a member to reduce their access.** Removal is destructive. Role changes are not.

## Where to next

- The full reference of who can do what is in [Understanding the three roles in practice](../8-collaboration/4-roles-in-practice.md).
- Manage the account itself, name, currency, and more, in [Account settings](../9-account-and-profile/6-account-settings.md).
- Running the instance for your club yourself? See [Self host KolleK with Docker](6-self-host-with-docker.md).
