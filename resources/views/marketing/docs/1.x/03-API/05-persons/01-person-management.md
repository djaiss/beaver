# Person management

:::markdown-actions url="{{docs.markdown_url}}"
:::copy-for-llm
:::/copy-for-llm

:::view-as-markdown url="{{docs.markdown_url}}"
:::/view-as-markdown
:::/markdown-actions

:::toc

- [List persons](#list-persons)
- [Create a person](#create-a-person)
- [Get a specific person](#get-a-specific-person)
- [Update a person](#update-a-person)
- [Delete a person](#delete-a-person)

:::/toc

:::section columns divided
:::column
The person management endpoints let you list, inspect, create, update, and delete the persons stored in a vault.

All endpoints require authentication and vault membership. Any vault member can list and inspect persons. Editors and owners can create, update, and delete persons. A person whose `can_be_deleted` attribute is `false` cannot be deleted.
:::/column

:::column
:::code title="Endpoints"

```text
GET    /api/vaults/{id}/persons
POST   /api/vaults/{id}/persons
GET    /api/vaults/{id}/persons/{person}
PUT    /api/vaults/{id}/persons/{person}
DELETE /api/vaults/{id}/persons/{person}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## List persons

:::description
This endpoint returns the persons in a vault, ordered from newest to oldest.

The response is paginated. The default page size is 10.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute
:::/parameters

:::parameters title="Query parameters"
:::attribute name="page" type="integer"
The page number to return.
:::/attribute

:::attribute name="per_page" type="integer"
The number of persons to return per page. The minimum is 1 and the maximum is 100.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="array"
The persons on the current page.
:::/attribute

:::attribute name="data[].type" type="string"
The resource type. This value is always `person`.
:::/attribute

:::attribute name="data[].id" type="string"
The ID of the person.
:::/attribute

:::attribute name="data[].attributes" type="object"
The person's attributes. See the single-person response for the complete shape.
:::/attribute

:::attribute name="data[].links.self" type="string"
The URL of the individual person endpoint.
:::/attribute

:::attribute name="links" type="object"
URLs for navigating between pages.
:::/attribute

:::attribute name="meta" type="object"
Pagination information.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/persons" verb="GET"

```json
{
  "data": [
    {
      "type": "person",
      "id": "42",
      "attributes": {
        "gender_id": "2",
        "kids_status": "has_kids",
        "slug": "42-regis-smith",
        "first_name": "Regis",
        "middle_name": "John",
        "last_name": "Smith",
        "nickname": "RJ",
        "maiden_name": null,
        "suffix": null,
        "prefix": "Mr.",
        "can_be_deleted": true,
        "is_listed": true,
        "created_at": 1781802000,
        "updated_at": 1781802000
      },
      "links": {
        "self": "{{app.url}}/api/vaults/1/persons/42"
      }
    }
  ],
  "links": {
    "first": "{{app.url}}/api/vaults/1/persons?page=1",
    "last": "{{app.url}}/api/vaults/1/persons?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "{{app.url}}/api/vaults/1/persons",
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

## Create a person

:::description
This endpoint creates a person and returns a `201 Created` response.

Only editors and owners can use this endpoint. When provided, `gender_id` must identify a gender from the same vault.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute
:::/parameters

:::parameters title="Body parameters"
:::attribute name="first_name" type="string" required
The person's first name. Maximum 100 characters.
:::/attribute

:::attribute name="gender_id" type="integer|null"
The ID of a gender in the vault.
:::/attribute

:::attribute name="kids_status" type="string|null"
The person's kids status: `no_kids`, `maybe_kids`, or `has_kids`.
:::/attribute

:::attribute name="middle_name" type="string|null"
The person's middle name. Maximum 100 characters.
:::/attribute

:::attribute name="last_name" type="string|null"
The person's last name. Maximum 100 characters.
:::/attribute

:::attribute name="nickname" type="string|null"
The person's nickname. Maximum 100 characters.
:::/attribute

:::attribute name="maiden_name" type="string|null"
The person's maiden name. Maximum 100 characters.
:::/attribute

:::attribute name="suffix" type="string|null"
The person's name suffix. Maximum 100 characters.
:::/attribute

:::attribute name="prefix" type="string|null"
The person's name prefix. Maximum 100 characters.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The newly created person resource.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/persons" verb="POST"

```json
{
  "gender_id": 2,
  "kids_status": "has_kids",
  "first_name": "Regis",
  "middle_name": "John",
  "last_name": "Smith",
  "nickname": "RJ",
  "maiden_name": null,
  "suffix": null,
  "prefix": "Mr."
}
```

```json
{
  "data": {
    "type": "person",
    "id": "42",
    "attributes": {
      "gender_id": "2",
      "kids_status": "has_kids",
      "slug": "42-regis-smith",
      "first_name": "Regis",
      "middle_name": "John",
      "last_name": "Smith",
      "nickname": "RJ",
      "maiden_name": null,
      "suffix": null,
      "prefix": "Mr.",
      "can_be_deleted": true,
      "is_listed": true,
      "created_at": 1781802000,
      "updated_at": 1781802000
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/persons/42"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Get a specific person

:::description
This endpoint returns a specific person from the vault.

A `404 Not Found` response is returned when the person does not exist or belongs to another vault.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="person" type="integer" required
The ID of the person.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data.type" type="string"
The resource type. This value is always `person`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the person.
:::/attribute

:::attribute name="data.attributes.gender_id" type="string|null"
The ID of the person's gender, or `null`.
:::/attribute

:::attribute name="data.attributes.kids_status" type="string|null"
The person's kids status.
:::/attribute

:::attribute name="data.attributes.slug" type="string|null"
The generated URL slug.
:::/attribute

:::attribute name="data.attributes.first_name" type="string|null"
The person's first name.
:::/attribute

:::attribute name="data.attributes.middle_name" type="string|null"
The person's middle name.
:::/attribute

:::attribute name="data.attributes.last_name" type="string|null"
The person's last name.
:::/attribute

:::attribute name="data.attributes.nickname" type="string|null"
The person's nickname.
:::/attribute

:::attribute name="data.attributes.maiden_name" type="string|null"
The person's maiden name.
:::/attribute

:::attribute name="data.attributes.suffix" type="string|null"
The person's name suffix.
:::/attribute

:::attribute name="data.attributes.prefix" type="string|null"
The person's name prefix.
:::/attribute

:::attribute name="data.attributes.can_be_deleted" type="boolean"
Whether the person can be deleted.
:::/attribute

:::attribute name="data.attributes.is_listed" type="boolean"
Whether the person is included in normal listings.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The creation time as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer|null"
The last update time as a Unix timestamp, or `null`.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of this endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/persons/{person}" verb="GET"

```json
{
  "data": {
    "type": "person",
    "id": "42",
    "attributes": {
      "gender_id": "2",
      "kids_status": "has_kids",
      "slug": "42-regis-smith",
      "first_name": "Regis",
      "middle_name": "John",
      "last_name": "Smith",
      "nickname": "RJ",
      "maiden_name": null,
      "suffix": null,
      "prefix": "Mr.",
      "can_be_deleted": true,
      "is_listed": true,
      "created_at": 1781802000,
      "updated_at": 1781802000
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/persons/42"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Update a person

:::description
This endpoint replaces the editable attributes of a person.

Only editors and owners can use this endpoint. `first_name` is required. Optional attributes omitted from the request are set to `null`.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="person" type="integer" required
The ID of the person.
:::/attribute
:::/parameters

:::parameters title="Body parameters"
:::attribute name="first_name" type="string" required
The person's first name. Maximum 100 characters.
:::/attribute

:::attribute name="gender_id" type="integer|null"
The ID of a gender in the vault, or `null`.
:::/attribute

:::attribute name="kids_status" type="string|null"
One of `no_kids`, `maybe_kids`, or `has_kids`.
:::/attribute

:::attribute name="middle_name" type="string|null"
The person's middle name. Maximum 100 characters.
:::/attribute

:::attribute name="last_name" type="string|null"
The person's last name. Maximum 100 characters.
:::/attribute

:::attribute name="nickname" type="string|null"
The person's nickname. Maximum 100 characters.
:::/attribute

:::attribute name="maiden_name" type="string|null"
The person's maiden name. Maximum 100 characters.
:::/attribute

:::attribute name="suffix" type="string|null"
The person's name suffix. Maximum 100 characters.
:::/attribute

:::attribute name="prefix" type="string|null"
The person's name prefix. Maximum 100 characters.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The updated person resource.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/persons/{person}" verb="PUT"

```json
{
  "gender_id": null,
  "kids_status": "maybe_kids",
  "first_name": "Regis",
  "middle_name": null,
  "last_name": "Updated",
  "nickname": "RJ",
  "maiden_name": null,
  "suffix": null,
  "prefix": "Mr."
}
```

```json
{
  "data": {
    "type": "person",
    "id": "42",
    "attributes": {
      "gender_id": null,
      "kids_status": "maybe_kids",
      "slug": "42-regis-updated",
      "first_name": "Regis",
      "middle_name": null,
      "last_name": "Updated",
      "nickname": "RJ",
      "maiden_name": null,
      "suffix": null,
      "prefix": "Mr.",
      "can_be_deleted": true,
      "is_listed": true,
      "created_at": 1781802000,
      "updated_at": 1781802600
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/persons/42"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns
:::column

## Delete a person

:::description
This endpoint permanently deletes a person and returns `204 No Content`.

Only editors and owners can use this endpoint. A `404 Not Found` response is returned when the person cannot be deleted.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="person" type="integer" required
The ID of the person.
:::/attribute
:::/parameters

:::parameters title="Body parameters" empty="This endpoint does not accept a request body."
:::/parameters

:::parameters title="Response attributes" empty="A successful response does not have a body."
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/persons/{person}" verb="DELETE"

```text
No response body
```

:::/code
:::/column
:::/section
