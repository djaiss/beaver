# Vault management

:::markdown-actions url="{{docs.markdown_url}}"
:::copy-for-llm
:::/copy-for-llm

:::view-as-markdown url="{{docs.markdown_url}}"
:::/view-as-markdown
:::/markdown-actions

:::toc

- [List vaults](#list-vaults)
- [Create a vault](#create-a-vault)
- [Get a specific vault](#get-a-specific-vault)
- [Update a vault](#update-a-vault)
- [Delete a vault](#delete-a-vault)
  :::/toc

:::section columns divided
:::column
The vault management endpoints let you list, create, inspect, rename, and delete vaults.

All endpoints require authentication. A user can only access vaults they belong to. Only the owner of a vault can update or delete it.
:::/column

:::column
:::code title="Endpoints"

```text
GET    /api/vaults
POST   /api/vaults
GET    /api/vaults/{id}
PUT    /api/vaults/{id}
DELETE /api/vaults/{id}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## List vaults

:::description
This endpoint returns the vaults the logged-in user belongs to, ordered by ID.

The response is paginated. The default page size is 10.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Query parameters"
:::attribute name="page" type="integer"
The page number to return. The first page is returned when this parameter is omitted.
:::/attribute

:::attribute name="per_page" type="integer"
The number of vaults to return per page. The default is 10, the minimum is 1, and the maximum is 100.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="array"
The vaults on the current page.
:::/attribute

:::attribute name="data[].type" type="string"
The type of the resource. This value is always `vault`.
:::/attribute

:::attribute name="data[].id" type="string"
The ID of the vault.
:::/attribute

:::attribute name="data[].attributes.name" type="string"
The name of the vault.
:::/attribute

:::attribute name="data[].attributes.avatar" type="string"
A generated SVG avatar encoded as a data URL.
:::/attribute

:::attribute name="data[].attributes.created_at" type="integer"
The time the vault was created, as a Unix timestamp.
:::/attribute

:::attribute name="data[].attributes.updated_at" type="integer"
The time the vault was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data[].links.self" type="string"
The URL of the individual vault endpoint.
:::/attribute

:::attribute name="links" type="object"
URLs for navigating between pages.
:::/attribute

:::attribute name="meta" type="object"
Pagination information, including the current page, page size, and total number of vaults.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults" verb="GET"

```json
{
  "data": [
    {
      "type": "vault",
      "id": "1",
      "attributes": {
        "name": "Central Perk",
        "avatar": "data:image/svg+xml;base64,PHN2Zy...",
        "created_at": 1771942777,
        "updated_at": 1771942777
      },
      "links": {
        "self": "{{app.url}}/api/vaults/1"
      }
    }
  ],
  "links": {
    "first": "{{app.url}}/api/vaults?page=1",
    "last": "{{app.url}}/api/vaults?page=2",
    "prev": null,
    "next": "{{app.url}}/api/vaults?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "path": "{{app.url}}/api/vaults",
    "per_page": 10,
    "to": 10,
    "total": 15
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Create a vault

:::description
This endpoint creates a vault and returns a `201 Created` response.

The logged-in user becomes the owner of the new vault.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Query parameters"
:::attribute name="name" type="string" required
The name of the vault. It may contain letters, numbers, spaces, hyphens, and underscores, and may contain at most 255 characters. Reserved names cannot be used.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The newly created vault.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `vault`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the vault.
:::/attribute

:::attribute name="data.attributes.name" type="string"
The sanitized name of the vault.
:::/attribute

:::attribute name="data.attributes.avatar" type="string"
A generated SVG avatar encoded as a data URL.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the vault was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer"
The time the vault was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual vault endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults" verb="POST"

```json
{
  "data": {
    "type": "vault",
    "id": "1",
    "attributes": {
      "name": "Central Perk",
      "avatar": "data:image/svg+xml;base64,PHN2Zy...",
      "created_at": 1771942777,
      "updated_at": 1771942777
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Get a specific vault

:::description
This endpoint returns a vault the logged-in user belongs to.

A `403 Forbidden` response is returned when the vault exists but the user is not a member. A missing vault returns `404 Not Found`.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault to return.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The vault resource.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `vault`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the vault.
:::/attribute

:::attribute name="data.attributes.name" type="string"
The name of the vault.
:::/attribute

:::attribute name="data.attributes.avatar" type="string"
A generated SVG avatar encoded as a data URL.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the vault was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer"
The time the vault was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual vault endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}" verb="GET"

```json
{
  "data": {
    "type": "vault",
    "id": "1",
    "attributes": {
      "name": "Central Perk",
      "avatar": "data:image/svg+xml;base64,PHN2Zy...",
      "created_at": 1771942777,
      "updated_at": 1771942777
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Update a vault

:::description
This endpoint renames a vault and returns the updated resource.

Only the owner of the vault can use this endpoint.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault to update.
:::/attribute
:::/parameters

:::parameters title="Query parameters"
:::attribute name="name" type="string" required
The new name of the vault. It may contain letters, numbers, spaces, hyphens, and underscores, and may contain at most 255 characters.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The updated vault.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `vault`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the vault.
:::/attribute

:::attribute name="data.attributes.name" type="string"
The updated, sanitized name of the vault.
:::/attribute

:::attribute name="data.attributes.avatar" type="string"
A generated SVG avatar encoded as a data URL.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the vault was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer"
The time the vault was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual vault endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}" verb="PUT"

```json
{
  "data": {
    "type": "vault",
    "id": "1",
    "attributes": {
      "name": "Central Perk Archives",
      "avatar": "data:image/svg+xml;base64,PHN2Zy...",
      "created_at": 1771942777,
      "updated_at": 1772029177
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns
:::column

## Delete a vault

:::description
This endpoint permanently deletes a vault and returns a `204 No Content` response. Please be certain.

Only the owner of the vault can use this endpoint.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault to delete.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes" empty="This endpoint does not return a response body."
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}" verb="DELETE"

```text
No response body
```

:::/code
:::/column
:::/section
