# Members

:::markdown-actions url="{{docs.markdown_url}}"
:::copy-for-llm
:::/copy-for-llm

:::view-as-markdown url="{{docs.markdown_url}}"
:::/view-as-markdown
:::/markdown-actions

:::toc

- [List the members of a vault](#list-the-members-of-a-vault)
- [Get a specific member](#get-a-specific-member)
- [Join a vault](#join-a-vault)
  :::/toc

:::section columns divided
:::column
The members endpoints let you review who belongs to a vault and let the authenticated user join a vault using an invitation code.

All endpoints require authentication. Listing and viewing members requires the authenticated user to be a member of the vault. Members are returned newest first, ordered by the time they joined.
:::/column

:::column
:::code title="Endpoints"

```text
GET  /api/vaults/{id}/members
GET  /api/vaults/{id}/members/{memberId}
POST /api/vaults/join
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## List the members of a vault

:::description
This endpoint returns the members of the given vault.

The response is paginated with 10 members per page. The authenticated user must be a member of the vault.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute
:::/parameters

:::parameters title="Query parameters"
:::attribute name="page" type="integer"
The page number to return. The first page is returned when this parameter is omitted.
:::/attribute

:::attribute name="per_page" type="integer"
The number of members to return per page. The default is 10, the minimum is 1 and the maximum is 100.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="array"
The members on the current page.
:::/attribute

:::attribute name="data[].type" type="string"
The type of the resource. This value is always `member`.
:::/attribute

:::attribute name="data[].id" type="string"
The ID of the member.
:::/attribute

:::attribute name="data[].attributes" type="object"
The attributes of the member.
:::/attribute

:::attribute name="data[].attributes.user_id" type="integer|null"
The ID of the user behind the membership.
:::/attribute

:::attribute name="data[].attributes.name" type="string|null"
The full name of the user behind the membership.
:::/attribute

:::attribute name="data[].attributes.email" type="string|null"
The email address of the user behind the membership.
:::/attribute

:::attribute name="data[].attributes.timezone" type="string|null"
The timezone of the member.
:::/attribute

:::attribute name="data[].attributes.joined_at" type="integer|null"
The time the user joined the vault, as a Unix timestamp.
:::/attribute

:::attribute name="data[].attributes.created_at" type="integer"
The time the member record was created, as a Unix timestamp.
:::/attribute

:::attribute name="data[].attributes.updated_at" type="integer|null"
The time the member record was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data[].links.self" type="string"
The URL of the individual member endpoint.
:::/attribute

:::attribute name="links" type="object"
URLs for navigating between pages.
:::/attribute

:::attribute name="meta" type="object"
Pagination information, including the current page, page size, and total number of members.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/members" verb="GET"

```json
{
  "data": [
    {
      "type": "member",
      "id": "42",
      "attributes": {
        "user_id": 18,
        "name": "Rachel Green",
        "email": "rachel.green@friends.test",
        "timezone": "America/New_York",
        "joined_at": 1751284800,
        "created_at": 1751284800,
        "updated_at": 1751284800
      },
      "links": {
        "self": "{{app.url}}/api/vaults/1/members/42"
      }
    }
  ],
  "links": {
    "first": "{{app.url}}/api/vaults/1/members?page=1",
    "last": "{{app.url}}/api/vaults/1/members?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "{{app.url}}/api/vaults/1/members",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Get a specific member

:::description
This endpoint returns a specific member of the given vault.

A `404 Not Found` response is returned when the member does not exist or belongs to another vault. The authenticated user must be a member of the vault.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="memberId" type="integer" required
The ID of the member to return.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The member resource.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `member`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the member.
:::/attribute

:::attribute name="data.attributes" type="object"
The attributes of the member.
:::/attribute

:::attribute name="data.attributes.user_id" type="integer|null"
The ID of the user behind the membership.
:::/attribute

:::attribute name="data.attributes.name" type="string|null"
The full name of the user behind the membership.
:::/attribute

:::attribute name="data.attributes.email" type="string|null"
The email address of the user behind the membership.
:::/attribute

:::attribute name="data.attributes.timezone" type="string|null"
The timezone of the member.
:::/attribute

:::attribute name="data.attributes.joined_at" type="integer|null"
The time the user joined the vault, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the member record was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer|null"
The time the member record was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual member endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/members/{memberId}" verb="GET"

```json
{
  "data": {
    "type": "member",
    "id": "43",
    "attributes": {
      "user_id": 19,
      "name": "Monica Geller",
      "email": "monica.geller@friends.test",
      "timezone": "America/New_York",
      "joined_at": 1751284800,
      "created_at": 1751284800,
      "updated_at": 1751284800
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/members/43"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns
:::column

## Join a vault

:::description
This endpoint lets the authenticated user join a vault using its invitation code. A new member is created and returned.

A `422 Unprocessable Entity` response is returned when the invitation code is missing, invalid, or when the user already belongs to the vault.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Body parameters"
:::attribute name="invitation_code" type="string" required
The invitation code of the vault to join. The maximum length is 64 characters.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The member resource that was created.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `member`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the member.
:::/attribute

:::attribute name="data.attributes" type="object"
The attributes of the member.
:::/attribute

:::attribute name="data.attributes.user_id" type="integer|null"
The ID of the user behind the membership.
:::/attribute

:::attribute name="data.attributes.name" type="string|null"
The full name of the user behind the membership.
:::/attribute

:::attribute name="data.attributes.email" type="string|null"
The email address of the user behind the membership.
:::/attribute

:::attribute name="data.attributes.timezone" type="string|null"
The timezone of the member.
:::/attribute

:::attribute name="data.attributes.joined_at" type="integer|null"
The time the user joined the vault, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the member record was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer|null"
The time the member record was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual member endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/join" verb="POST"

```json
{
  "data": {
    "type": "member",
    "id": "44",
    "attributes": {
      "user_id": 20,
      "name": "Chandler Bing",
      "email": "chandler.bing@friends.test",
      "timezone": "America/New_York",
      "joined_at": 1751284800,
      "created_at": 1751284800,
      "updated_at": 1751284800
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/members/44"
    }
  }
}
```

:::/code
:::/column
:::/section
