# API management

:::markdown-actions url="{{docs.markdown_url}}"
:::copy-for-llm
:::/copy-for-llm

:::view-as-markdown url="{{docs.markdown_url}}"
:::/view-as-markdown
:::/markdown-actions

:::toc

- [List API keys](#list-api-keys)
- [Get a specific API key](#get-a-specific-api-key)
- [Create an API key](#create-an-api-key)
- [Delete an API key](#delete-an-api-key)
  :::/toc

:::section columns divided
:::column
The API management endpoints let you create, inspect, and revoke API keys belonging to the logged-in user.

API keys provide access to sensitive account data. Store newly created keys securely and never expose them in client-side code or public repositories.

Tokens are only shown when they are created and cannot be retrieved again. If you lose a token, you must revoke the corresponding API key and create a new one.
:::/column

:::column
:::code title="Endpoints"

```text
GET    /api/administration/api
GET    /api/administration/api/{id}
POST   /api/administration/api
DELETE /api/administration/api/{id}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## List API keys

:::description
This endpoint returns every API key belonging to the user who owns the API key used for authentication.

The plaintext API keys are not returned. This endpoint is not paginated.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="array"
The user's API keys.
:::/attribute

:::attribute name="data[].type" type="string"
The type of the resource. This value is always `api_key`.
:::/attribute

:::attribute name="data[].id" type="string"
The ID of the API key.
:::/attribute

:::attribute name="data[].attributes.name" type="string"
The label assigned to the API key.
:::/attribute

:::attribute name="data[].attributes.token" type="null"
The plaintext API key. This value is always `null` when listing existing keys.
:::/attribute

:::attribute name="data[].attributes.last_used_at" type="integer|null"
The last time the API key was used, as a Unix timestamp, or `null` if it has never been used.
:::/attribute

:::attribute name="data[].attributes.created_at" type="integer"
The time the API key was created, as a Unix timestamp.
:::/attribute

:::attribute name="data[].attributes.updated_at" type="integer"
The time the API key was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data[].links.self" type="string"
The URL of the individual API key endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/administration/api" verb="GET"

```json
{
  "data": [
    {
      "type": "api_key",
      "id": "1",
      "attributes": {
        "name": "Mobile app",
        "token": null,
        "last_used_at": null,
        "created_at": 1751328000,
        "updated_at": 1751328000
      },
      "links": {
        "self": "{{app.url}}/api/administration/api/1"
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

## Get a specific API key

:::description
This endpoint returns a specific API key belonging to the user who owns the API key used for authentication.

The plaintext API key is not returned. A `404 Not Found` response is returned when the key does not exist or belongs to another user.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the API key to return.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The API key resource.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `api_key`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the API key.
:::/attribute

:::attribute name="data.attributes.name" type="string"
The label assigned to the API key.
:::/attribute

:::attribute name="data.attributes.token" type="null"
The plaintext API key. This value is always `null` when retrieving an existing key.
:::/attribute

:::attribute name="data.attributes.last_used_at" type="integer|null"
The last time the API key was used, as a Unix timestamp, or `null` if it has never been used.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the API key was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer"
The time the API key was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual API key endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/administration/api/{id}" verb="GET"

```json
{
  "data": {
    "type": "api_key",
    "id": "1",
    "attributes": {
      "name": "Mobile app",
      "token": null,
      "last_used_at": 1751328000,
      "created_at": 1750896000,
      "updated_at": 1751328000
    },
    "links": {
      "self": "{{app.url}}/api/administration/api/1"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Create an API key

:::description
This endpoint creates a new API key and returns a `201 Created` response.

The plaintext token is only available in this response. Store it securely because it cannot be retrieved again.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Query parameters"
:::attribute name="label" type="string" required
A recognizable label for the API key. It must contain plain text and may contain at most 255 characters.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The newly created API key resource.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `api_key`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the API key.
:::/attribute

:::attribute name="data.attributes.name" type="string"
The sanitized label assigned to the API key.
:::/attribute

:::attribute name="data.attributes.token" type="string"
The newly created plaintext API key.
:::/attribute

:::attribute name="data.attributes.last_used_at" type="null"
The last-used timestamp. This value is `null` for a newly created key.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the API key was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer"
The time the API key was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual API key endpoint.
:::/attribute

:::attribute name="token" type="string"
The newly created plaintext API key, also provided at the top level of the response.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/administration/api" verb="POST"

```json
{
  "data": {
    "type": "api_key",
    "id": "8",
    "attributes": {
      "name": "Production integration",
      "token": "8|YOUR_NEW_API_KEY",
      "last_used_at": null,
      "created_at": 1751328000,
      "updated_at": 1751328000
    },
    "links": {
      "self": "{{app.url}}/api/administration/api/8"
    }
  },
  "token": "8|YOUR_NEW_API_KEY"
}
```

:::/code
:::/column
:::/section

:::section columns
:::column

## Delete an API key

:::description
This endpoint permanently revokes an API key belonging to the logged-in user and returns a `204 No Content` response.

If the request is authenticated with the API key being deleted, that key cannot be used for subsequent requests.
:::/description

:::parameters title="URL parameters"
:::attribute name="id" type="integer" required
The ID of the API key to delete.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes" empty="This endpoint does not return a response body."
:::/parameters
:::/column

:::column
:::code title="/api/administration/api/{id}" verb="DELETE"

```text
No response body
```

:::/code
:::/column
:::/section
