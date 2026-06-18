# Relationship type management

:::markdown-actions url="{{docs.markdown_url}}"
:::copy-for-llm
:::/copy-for-llm

:::view-as-markdown url="{{docs.markdown_url}}"
:::/view-as-markdown
:::/markdown-actions

:::toc

- [List relationship type categories](#list-relationship-type-categories)
- [Create a relationship type category](#create-a-relationship-type-category)
- [Get a relationship type category](#get-a-relationship-type-category)
- [Update a relationship type category](#update-a-relationship-type-category)
- [Delete a relationship type category](#delete-a-relationship-type-category)
- [List relationship types](#list-relationship-types)
- [Create a relationship type](#create-a-relationship-type)
- [Get a relationship type](#get-a-relationship-type)
- [Update a relationship type](#update-a-relationship-type)
- [Delete a relationship type](#delete-a-relationship-type)
  :::/toc

:::section columns divided
:::column
Relationship type categories organize the relationship types available in a vault. Relationship types describe how two people are connected, such as friend, sibling, or parent and child.

All endpoints require authentication and vault membership. Any vault member can list and inspect categories and types. Only the vault owner can create, update, reorder, or delete them.

Relationship types are nested under a category. Directional types use different forward and reverse names, such as `Parent` and `Child`.
:::/column

:::column
:::code title="Endpoints"

```text
GET    /api/vaults/{id}/relationship-type-categories
POST   /api/vaults/{id}/relationship-type-categories
GET    /api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}
PUT    /api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}
DELETE /api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}

GET    /api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}/relationship-types
POST   /api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}/relationship-types
GET    /api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}/relationship-types/{relationshipType}
PUT    /api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}/relationship-types/{relationshipType}
DELETE /api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}/relationship-types/{relationshipType}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## List relationship type categories

:::description
Returns the vault's relationship type categories, ordered by position. The response is paginated.
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
The number of categories per page. The default is 10, the minimum is 1, and the maximum is 100.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="array"
The categories on the current page.
:::/attribute

:::attribute name="data[].type" type="string"
Always `relationship_type_category`.
:::/attribute

:::attribute name="data[].id" type="string"
The category ID.
:::/attribute

:::attribute name="data[].attributes.key" type="string"
The stable internal key.
:::/attribute

:::attribute name="data[].attributes.name" type="string"
The translated or custom category name.
:::/attribute

:::attribute name="data[].attributes.position" type="integer"
The category's position.
:::/attribute

:::attribute name="data[].attributes.can_be_deleted" type="boolean"
Whether the category may be deleted.
:::/attribute

:::attribute name="data[].attributes.created_at" type="integer"
The creation time as a Unix timestamp.
:::/attribute

:::attribute name="data[].attributes.updated_at" type="integer|null"
The last update time as a Unix timestamp, or `null`.
:::/attribute

:::attribute name="data[].links.self" type="string"
The individual category endpoint.
:::/attribute

:::attribute name="links" type="object"
Pagination links.
:::/attribute

:::attribute name="meta" type="object"
Pagination metadata.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/relationship-type-categories" verb="GET"

```json
{
  "data": [
    {
      "type": "relationship_type_category",
      "id": "1",
      "attributes": {
        "key": "family",
        "name": "Family",
        "position": 1,
        "can_be_deleted": false,
        "created_at": 1771942777,
        "updated_at": 1771942777
      },
      "links": {
        "self": "{{app.url}}/api/vaults/1/relationship-type-categories/1"
      }
    }
  ]
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Create a relationship type category

:::description
Creates a custom category at the end of the ordered category list. Only the vault owner can use this endpoint.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute
:::/parameters

:::parameters title="Body parameters"
:::attribute name="name" type="string" required
The category name. It must contain between 3 and 100 characters.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The newly created relationship type category.
:::/attribute

:::attribute name="data.type" type="string"
Always `relationship_type_category`.
:::/attribute

:::attribute name="data.id" type="string"
The category ID.
:::/attribute

:::attribute name="data.attributes" type="object"
The category attributes described by the list endpoint.
:::/attribute

:::attribute name="data.links.self" type="string"
The individual category endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/relationship-type-categories" verb="POST"

```json
{
  "name": "Extended family"
}
```

```json
{
  "data": {
    "type": "relationship_type_category",
    "id": "13",
    "attributes": {
      "key": "custom-a1b2c3d4e5f6g7h8",
      "name": "Extended family",
      "position": 13,
      "can_be_deleted": true,
      "created_at": 1771942777,
      "updated_at": 1771942777
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/relationship-type-categories/13"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Get a relationship type category

:::description
Returns one category from the vault. A category from another vault returns `404 Not Found`.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="relationshipTypeCategory" type="integer" required
The ID of the category.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The relationship type category.
:::/attribute

:::attribute name="data.attributes" type="object"
The category attributes described by the list endpoint.
:::/attribute

:::attribute name="data.links.self" type="string"
The individual category endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}" verb="GET"

```json
{
  "data": {
    "type": "relationship_type_category",
    "id": "1",
    "attributes": {
      "key": "family",
      "name": "Family",
      "position": 1,
      "can_be_deleted": false,
      "created_at": 1771942777,
      "updated_at": 1771942777
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/relationship-type-categories/1"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Update a relationship type category

:::description
Updates a category's name and optionally its position. Only the vault owner can use this endpoint.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="relationshipTypeCategory" type="integer" required
The ID of the category.
:::/attribute
:::/parameters

:::parameters title="Body parameters"
:::attribute name="name" type="string" required
The category name. It must contain between 3 and 100 characters.
:::/attribute

:::attribute name="position" type="integer"
The new position. Other categories are shifted to preserve ordering.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The updated category.
:::/attribute

:::attribute name="data.attributes" type="object"
The category attributes described by the list endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}" verb="PUT"

```json
{
  "name": "Close family",
  "position": 2
}
```

```json
{
  "data": {
    "type": "relationship_type_category",
    "id": "13",
    "attributes": {
      "key": "custom-a1b2c3d4e5f6g7h8",
      "name": "Close family",
      "position": 2,
      "can_be_deleted": true,
      "created_at": 1771942777,
      "updated_at": 1771943000
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/relationship-type-categories/13"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Delete a relationship type category

:::description
Deletes a custom category and its relationship types. Only the vault owner can use this endpoint. Protected default categories return `404 Not Found`.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="relationshipTypeCategory" type="integer" required
The ID of the category.
:::/attribute
:::/parameters

:::parameters title="Body parameters" empty="No body parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes" empty="A successful request returns 204 No Content."
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}" verb="DELETE"

```text
No response body
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## List relationship types

:::description
Returns the relationship types in a category, ordered by position. The response is paginated.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="relationshipTypeCategory" type="integer" required
The ID of the category.
:::/attribute
:::/parameters

:::parameters title="Query parameters"
:::attribute name="page" type="integer"
The page number to return.
:::/attribute

:::attribute name="per_page" type="integer"
The number of types per page. The default is 10, the minimum is 1, and the maximum is 100.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="array"
The relationship types on the current page.
:::/attribute

:::attribute name="data[].type" type="string"
Always `relationship_type`.
:::/attribute

:::attribute name="data[].id" type="string"
The relationship type ID.
:::/attribute

:::attribute name="data[].attributes.relationship_type_category_id" type="string"
The parent category ID.
:::/attribute

:::attribute name="data[].attributes.key" type="string"
The stable internal key.
:::/attribute

:::attribute name="data[].attributes.name" type="string"
The general relationship type name.
:::/attribute

:::attribute name="data[].attributes.forward_name" type="string"
The name from source to target.
:::/attribute

:::attribute name="data[].attributes.reverse_name" type="string"
The name from target to source.
:::/attribute

:::attribute name="data[].attributes.is_directed" type="boolean"
Whether direction changes the meaning.
:::/attribute

:::attribute name="data[].attributes.can_be_deleted" type="boolean"
Whether the type may be deleted.
:::/attribute

:::attribute name="data[].attributes.position" type="integer"
The position within the category.
:::/attribute

:::attribute name="data[].attributes.created_at" type="integer"
The creation time as a Unix timestamp.
:::/attribute

:::attribute name="data[].attributes.updated_at" type="integer|null"
The last update time as a Unix timestamp, or `null`.
:::/attribute

:::attribute name="data[].links.self" type="string"
The individual relationship type endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}/relationship-types" verb="GET"

```json
{
  "data": [
    {
      "type": "relationship_type",
      "id": "1",
      "attributes": {
        "relationship_type_category_id": "1",
        "key": "parent_child",
        "name": "Parent / child",
        "forward_name": "Parent",
        "reverse_name": "Child",
        "is_directed": true,
        "can_be_deleted": false,
        "position": 1,
        "created_at": 1771942777,
        "updated_at": 1771942777
      },
      "links": {
        "self": "{{app.url}}/api/vaults/1/relationship-type-categories/1/relationship-types/1"
      }
    }
  ]
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Create a relationship type

:::description
Creates a relationship type at the end of the category. Only the vault owner can use this endpoint.

When `is_directed` is `true`, both `forward_name` and `reverse_name` are required.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="relationshipTypeCategory" type="integer" required
The ID of the category.
:::/attribute
:::/parameters

:::parameters title="Body parameters"
:::attribute name="name" type="string" required
The general name. It must contain between 3 and 100 characters.
:::/attribute

:::attribute name="is_directed" type="boolean"
Whether direction changes the meaning. Defaults to `false`.
:::/attribute

:::attribute name="forward_name" type="string"
The source-to-target name. Required when `is_directed` is `true`.
:::/attribute

:::attribute name="reverse_name" type="string"
The target-to-source name. Required when `is_directed` is `true`.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The newly created relationship type.
:::/attribute

:::attribute name="data.attributes" type="object"
The relationship type attributes described by the list endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}/relationship-types" verb="POST"

```json
{
  "name": "Parent / child",
  "is_directed": true,
  "forward_name": "Parent",
  "reverse_name": "Child"
}
```

```json
{
  "data": {
    "type": "relationship_type",
    "id": "46",
    "attributes": {
      "relationship_type_category_id": "1",
      "key": "custom-h1i2j3k4l5m6n7o8",
      "name": "Parent / child",
      "forward_name": "Parent",
      "reverse_name": "Child",
      "is_directed": true,
      "can_be_deleted": true,
      "position": 8,
      "created_at": 1771942777,
      "updated_at": 1771942777
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/relationship-type-categories/1/relationship-types/46"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Get a relationship type

:::description
Returns one relationship type. The type must belong to the category and vault in the URL.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="relationshipTypeCategory" type="integer" required
The ID of the category.
:::/attribute

:::attribute name="relationshipType" type="integer" required
The ID of the relationship type.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The relationship type.
:::/attribute

:::attribute name="data.attributes" type="object"
The relationship type attributes described by the list endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}/relationship-types/{relationshipType}" verb="GET"

```json
{
  "data": {
    "type": "relationship_type",
    "id": "1",
    "attributes": {
      "relationship_type_category_id": "1",
      "key": "parent_child",
      "name": "Parent / child",
      "forward_name": "Parent",
      "reverse_name": "Child",
      "is_directed": true,
      "can_be_deleted": false,
      "position": 1,
      "created_at": 1771942777,
      "updated_at": 1771942777
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/relationship-type-categories/1/relationship-types/1"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Update a relationship type

:::description
Updates a relationship type and optionally changes its position within the category. Only the vault owner can use this endpoint.

Setting `is_directed` to `false` clears custom forward and reverse names.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="relationshipTypeCategory" type="integer" required
The ID of the category.
:::/attribute

:::attribute name="relationshipType" type="integer" required
The ID of the relationship type.
:::/attribute
:::/parameters

:::parameters title="Body parameters"
:::attribute name="name" type="string" required
The general name. It must contain between 3 and 100 characters.
:::/attribute

:::attribute name="is_directed" type="boolean"
Whether direction changes the meaning.
:::/attribute

:::attribute name="forward_name" type="string"
The source-to-target name. Required when `is_directed` is explicitly `true`.
:::/attribute

:::attribute name="reverse_name" type="string"
The target-to-source name. Required when `is_directed` is explicitly `true`.
:::/attribute

:::attribute name="position" type="integer"
The new position within the category.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The updated relationship type.
:::/attribute

:::attribute name="data.attributes" type="object"
The relationship type attributes described by the list endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}/relationship-types/{relationshipType}" verb="PUT"

```json
{
  "name": "Guardian / ward",
  "is_directed": true,
  "forward_name": "Guardian",
  "reverse_name": "Ward",
  "position": 2
}
```

```json
{
  "data": {
    "type": "relationship_type",
    "id": "46",
    "attributes": {
      "relationship_type_category_id": "1",
      "key": "custom-h1i2j3k4l5m6n7o8",
      "name": "Guardian / ward",
      "forward_name": "Guardian",
      "reverse_name": "Ward",
      "is_directed": true,
      "can_be_deleted": true,
      "position": 2,
      "created_at": 1771942777,
      "updated_at": 1771943000
    },
    "links": {
      "self": "{{app.url}}/api/vaults/1/relationship-type-categories/1/relationship-types/46"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns
:::column

## Delete a relationship type

:::description
Deletes a custom relationship type. Only the vault owner can use this endpoint. Protected default types return `404 Not Found`.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the vault.
:::/attribute

:::attribute name="relationshipTypeCategory" type="integer" required
The ID of the category.
:::/attribute

:::attribute name="relationshipType" type="integer" required
The ID of the relationship type.
:::/attribute
:::/parameters

:::parameters title="Body parameters" empty="No body parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes" empty="A successful request returns 204 No Content."
:::/parameters
:::/column

:::column
:::code title="/api/vaults/{id}/relationship-type-categories/{relationshipTypeCategory}/relationship-types/{relationshipType}" verb="DELETE"

```text
No response body
```

:::/code
:::/column
:::/section
