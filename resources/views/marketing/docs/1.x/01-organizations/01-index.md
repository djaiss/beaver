# Organizations

## What is an organization?

An organization is the central workspace in libraryOS. Everything — members, offices, departments, and all administrative settings — lives inside an organization. Think of it as your company's home inside the product.

When Michael Scott sets up Dunder Mifflin Paper Company in libraryOS, he is creating an organization. Every person he invites, every office he registers, and every department he defines all belong to that one organization.

An organization has a **name** and a unique **slug** that is used in URLs. It also has an **invitation code** — a secret string that lets other people join it.

## Roles and permissions

Every person inside an organization has a role. The role determines what that person can see and do. libraryOS defines four roles, from most to least privileged:

| Role          | What they can do                                                                                                                              |
| ------------- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| Owner         | Full access. Can manage everything in Adminland, including deleting the organization. Typically the person who created it.                    |
| Administrator | Can manage most Adminland settings — offices, office types, departments, member types, and members — but cannot delete the organization.      |
| Member        | Can view the organization and its data. Cannot modify Adminland settings. This is the default role when someone joins via an invitation code. |
| Guest         | Limited read-only access. Useful for contractors or external collaborators who need visibility without full membership.                       |

The person who creates an organization is automatically given the **Owner** role. Owners and Administrators can update the roles of other members from Adminland.

## Creating an organization

Any authenticated user can create a new organization. Once created, that user becomes its **Owner**.

1. From your dashboard, click **Create an organization**.
2. Enter the organization name. Names may contain letters, numbers, spaces, hyphens, and underscores.
3. Save. libraryOS will automatically generate a unique URL slug and an invitation code for you.

After creation, libraryOS runs a background job to pre-populate the organization with sensible defaults so you can get started right away.

## Joining an organization

You do not need to create your own organization to use libraryOS. If your company is already set up, an Owner or Administrator can share the organization's **invitation code** with you, and you can use it to join.

When Pam Beesly joins Dunder Mifflin in libraryOS, Michael simply gives her the invitation code. She enters it on the _Join an organization_ screen, and she is immediately added as a **Member**.

1. From your dashboard, click **Join an organization**.
2. Paste or type the invitation code you received.
3. Confirm. You will be added to the organization as a Member and taken to its dashboard.

The invitation code is a long random string — keep it private. Anyone who has it can join the organization. If the code is ever compromised, an Owner or Administrator can regenerate it from Adminland.

You cannot join an organization you are already a member of. libraryOS will tell you so and prevent a duplicate membership from being created.

## Belonging to multiple organizations

A single user account can belong to more than one organization simultaneously. This is useful for consultants, contractors, or anyone who works across multiple companies.

Each membership is independent — your role in one organization has no effect on your role in another. You can switch between organizations from your dashboard at any time.

## Adminland

**Adminland** is the administration area of your organization. It is only accessible to users with the **Owner** or **Administrator** role.

From Adminland you can manage:

- [Office types](/docs/1.x/offices) — the categories used to classify your offices
- [Offices](/docs/1.x/offices) — the physical and virtual locations your organization operates from
- [Departments](/docs/1.x/departments) — the functional groups that make up your organization
- Members and their roles
- Member types

Regular members can view the organization but cannot access or modify any Adminland settings.
