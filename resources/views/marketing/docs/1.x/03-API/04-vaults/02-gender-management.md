# Gender management

:::markdown-actions url="{{docs.markdown_url}}"
:::copy-for-llm
:::/copy-for-llm

:::view-as-markdown url="{{docs.markdown_url}}"
:::/view-as-markdown
:::/markdown-actions

:::toc

- [List genders](#list-genders)
- [Get a specific gender](#get-a-specific-gender)
- [Create a gender](#create-a-gender)
- [Update a gender](#update-a-gender)
- [Delete a gender](#delete-a-gender)
  :::/toc

:::section columns divided
:::column
The gender management endpoints let you list, inspect, create, rename, and delete the genders available in a vault.

All endpoints require authentication and vault membership. Any vault member can list and inspect genders. Only the vault owner can create, update, or delete them.
:::/column

:::column
:::code title="Endpoints"

```text
GET    /api/vaults/{id}/genders
GET    /api/vaults/{id}/genders/{gender}
POST   /api/vaults/{id}/genders
PUT    /api/vaults/{id}/genders/{gender}
DELETE /api/vaults/{id}/genders/{gender}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## List genders

:::description
This endpoint returns every gender in the vault, ordered by position.

The response is paginated. The default page size is 10.
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
The number of genders to return per page. The default is 10, the minimum is 1, and the maximum is 100.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="array"
The genders on the current page, ordered by position.
:::/attribute

:::attribute name="data[].type" type="string"
The type of the resource. This value is always `gender`.
:::/attribute

:::attribute name="data[].id" type="string"
The ID of the gender.
:::/attribute

:::attribute name="data[].attributes.name" type="string"
The name of the gender.
:::/attribute

:::attribute name="data[].attributes.position" type="integer"
The gender's position in the vault's ordered list.
:::/attribute

:::attribute name="data[].attributes.created_at" type="integer"
The time the gender was created, as a Unix timestamp.
:::/attribute

:::attribute name="data[].attributes.updated_at" type="integer|null"
The time the gender was last updated, as a Unix timestamp, or `null`.
:::/attribute

:::attribute name="data[].links.self" type="string"
The URL of the individual gender endpoint.
:::/attribute

:::attribute name="links" type="object"
URLs for navigating between pages.
:::/attribute

:::attribute name="meta" type="object"
Pagination information, including the current page, page size, and total number of genders.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/genders" verb="GET"

```json
{
  "data": [
    {
      "type": "gender",
      "id": "1",
      "attributes": {
        "name": "Woman",
        "position": 1,
        "created_at": 1771942777,
        "updated_at": 1771942777
      },
      "links": {
        "self": "{{app.url}}/api/vaults/1/genders/1"
      }
    },
    {
      "type": "gender",
      "id": "2",
      "attributes": {
        "name": "Man",
        "position": 2,
        "created_at": 1771942777,
        "updated_at": 1771942777
      },
      "links": {
        "self": "{{app.url}}/api/vaults/1/genders/2"
      }
    }
  ],
  "links": {
    "first": "{{app.url}}/api/vaults/1/genders?page=1",
    "last": "{{app.url}}/api/vaults/1/genders?page=2",
    "prev": null,
    "next": "{{app.url}}/api/vaults/1/genders?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "path": "{{app.url}}/api/vaults/1/genders",
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

## Get a specific gender

:::description
This endpoint returns a specific gender from the vault.

A `404 Not Found` response is returned when the gender does not exist or belongs to another vault.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="gender" type="integer" required
The ID of the gender to return.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The gender resource.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `gender`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the gender.
:::/attribute

:::attribute name="data.attributes.name" type="string"
The name of the gender.
:::/attribute

:::attribute name="data.attributes.position" type="integer"
The gender's position in the vault's ordered list.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the gender was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer|null"
The time the gender was last updated, as a Unix timestamp, or `null`.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual gender endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/genders/{gender}" verb="GET"

```json
{
  "data": {
    "type": "gender",
    "id": "1",
    "attributes": {
      "name": "Woman",
      "position": 1,
      "created_at": 1771942777,
      "updated_at": 1771942777
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/genders/1"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Create a gender

:::description
This endpoint creates a gender and returns a `201 Created` response.

Only the vault owner can use this endpoint. The new gender is placed at the end of the vault's ordered gender list.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute
:::/parameters

:::parameters title="Query parameters"
:::attribute name="name" type="string" required
The name of the gender. It must contain between 3 and 100 characters.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The newly created gender.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `gender`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the gender.
:::/attribute

:::attribute name="data.attributes.name" type="string"
The sanitized name of the gender.
:::/attribute

:::attribute name="data.attributes.position" type="integer"
The gender's position in the vault's ordered list.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the gender was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer|null"
The time the gender was last updated, as a Unix timestamp, or `null`.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual gender endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/genders" verb="POST"

```json
{
  "data": {
    "type": "gender",
    "id": "3",
    "attributes": {
      "name": "Non-binary",
      "position": 3,
      "created_at": 1771942777,
      "updated_at": 1771942777
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/genders/3"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Update a gender

:::description
This endpoint renames a gender and returns the updated resource.

Only the vault owner can use this endpoint. The gender's position is not changed.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="gender" type="integer" required
The ID of the gender to update.
:::/attribute
:::/parameters

:::parameters title="Query parameters"
:::attribute name="name" type="string" required
The new name of the gender. It must contain between 3 and 100 characters.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The updated gender.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `gender`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the gender.
:::/attribute

:::attribute name="data.attributes.name" type="string"
The updated, sanitized name of the gender.
:::/attribute

:::attribute name="data.attributes.position" type="integer"
The gender's unchanged position in the vault's ordered list.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the gender was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer|null"
The time the gender was last updated, as a Unix timestamp, or `null`.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual gender endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/genders/{gender}" verb="PUT"

```json
{
  "data": {
    "type": "gender",
    "id": "3",
    "attributes": {
      "name": "Genderqueer",
      "position": 3,
      "created_at": 1771942777,
      "updated_at": 1772029177
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/genders/3"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns
:::column

## Delete a gender

:::description
This endpoint permanently deletes a gender and returns a `204 No Content` response.

Only the vault owner can use this endpoint.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="gender" type="integer" required
The ID of the gender to delete.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes" empty="This endpoint does not return a response body."
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/genders/{gender}" verb="DELETE"

```text
No response body
```

:::/code
:::/column
:::/section
