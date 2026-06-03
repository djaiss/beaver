# Manage offices

This page explains how to create, edit, and delete [offices and office types](/docs/1.x/offices) in your organization.

## Requirements

Only users with the **Owner** or **Administrator** role can create, edit, or delete offices and office types. Members and Guests can view the list but cannot make changes. For more information on roles, see [Roles and permissions](/docs/1.x/organizations#roles-and-permissions).

## Manage office types

### Add an office type

1. Open your organization's dashboard and click **Adminland** in the navigation.
2. In the Adminland sidebar, click **Offices**.
3. On the Offices page you will see an **Office types** section at the top. Click the **Add** button on the right side of that section header.
4. A form appears inline. Fill in the following field:
   1. **Name** (required) — the name for the office type. Example: _Branch_, _Headquarters_, or _Warehouse_.
5. Click **Create** to save. The new office type appears at the bottom of the list.

### Edit an office type

1. Open your organization's dashboard and click **Adminland**.
2. In the Adminland sidebar, click **Offices**.
3. In the **Office types** section, hover over the office type you want to change. An **Edit** button appears on the right. Click it.
4. An edit form replaces the row inline. You can update:
   1. **Name** — the new name for the office type.
5. Click **Update** to save your changes.

### Delete an office type

Deleting an office type removes the classification from the system. Any offices currently assigned that type are not deleted — they simply lose their office type and become unclassified.

1. Open your organization's dashboard and click **Adminland**.
2. In the Adminland sidebar, click **Offices**.
3. In the **Office types** section, hover over the office type you want to remove. A **Delete** button appears on the right. Click it.
4. A confirmation prompt appears. Confirm to permanently delete the office type.

## Manage offices

### Add an office

1. Open your organization's dashboard and click **Adminland** in the navigation.
2. In the Adminland sidebar, click **Offices**.
3. Scroll past the Office types section to the **Offices** section. Click the **Add** button on the right side of the section header.
4. A form appears inline. Fill in the fields:
   1. **Name** (required) — a recognizable label for the office. Example: _Scranton Branch_ or _New York Headquarters_.
   2. **Office type** (optional) — select a classification from the dropdown. Leave blank if you do not need a classification.
   3. **Address line 1** (required) — the street address. Example: _1725 Slough Avenue_.
   4. **Address line 2** (optional) — suite number, floor, or building identifier.
   5. **City** (required) — the city where the office is located. Example: _Scranton_.
   6. **State / Province** (optional) — the state, province, or region. Example: _PA_.
   7. **Postal code** (optional) — the ZIP or postal code. Example: _18505_.
   8. **Timezone** (optional) — the IANA time zone identifier for this location. Example: _America/New_York_, _Europe/London_, or _Asia/Tokyo_.
   9. **Country** (optional) — select the country from the dropdown list.
5. Click **Create** to save. The new office appears in the list.

### Edit an office

1. Open your organization's dashboard and click **Adminland**.
2. In the Adminland sidebar, click **Offices**.
3. In the **Offices** section, hover over the office you want to update. An **Edit** button appears on the right. Click it.
4. An edit form replaces the row inline. Update any of the fields described in the [Add an office](#add-an-office) section above.
5. Click **Update** to save your changes.

### Delete an office

Deleting an office permanently removes the location record from the organization. Any members currently associated with that office lose the association and are not themselves deleted.

1. Open your organization's dashboard and click **Adminland**.
2. In the Adminland sidebar, click **Offices**.
3. In the **Offices** section, hover over the office you want to remove. A **Delete** button appears on the right. Click it.
4. A confirmation prompt appears. Confirm to permanently delete the office.
