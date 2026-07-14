# Logs

:::markdown-actions url="{{docs.markdown_url}}"
:::copy-for-llm
:::/copy-for-llm

:::view-as-markdown url="{{docs.markdown_url}}"
:::/view-as-markdown
:::/markdown-actions

:::toc

- [List the logged-in user's logs](#list-the-logged-in-users-logs)
- [Get a specific log](#get-a-specific-log)
  :::/toc

:::section columns divided
:::column
The logs endpoints let you review actions associated with the logged-in user.

Both endpoints require authentication. A user can only access their own logs. Logs are returned newest first.
:::/column

:::column
:::code title="Endpoints"

```text
GET /api/administration/logs
GET /api/administration/logs/{log}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## List the logged-in user's logs

:::description
This endpoint returns the logs associated with the user who owns the API key.

The response is paginated with 10 logs per page.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Query parameters"
:::attribute name="page" type="integer"
The page number to return. The first page is returned when this parameter is omitted.
:::/attribute

:::attribute name="per_page" type="integer"
The number of logs to return per page. The default is 10, the minimum is 1 and the maximum is 100.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="array"
The logs on the current page.
:::/attribute

:::attribute name="data[].type" type="string"
The type of the resource. This value is always `log`.
:::/attribute

:::attribute name="data[].id" type="string"
The ID of the log.
:::/attribute

:::attribute name="data[].attributes" type="object"
The attributes of the log.
:::/attribute

:::attribute name="data[].attributes.user_name" type="string"
The current name of the user, or their name when the log was created if the user no longer exists.
:::/attribute

:::attribute name="data[].attributes.action" type="string"
The machine-readable name of the action.
:::/attribute

:::attribute name="data[].attributes.parameters" type="object|null"
Values recorded for the action and used to produce its description.
:::/attribute

:::attribute name="data[].attributes.description" type="string"
A human-readable description of the action.
:::/attribute

:::attribute name="data[].attributes.created_at" type="integer"
The time the log was created, as a Unix timestamp.
:::/attribute

:::attribute name="data[].attributes.updated_at" type="integer|null"
The time the log was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data[].links.self" type="string"
The URL of the individual log endpoint.
:::/attribute

:::attribute name="links" type="object"
URLs for navigating between pages.
:::/attribute

:::attribute name="meta" type="object"
Pagination information, including the current page, page size, and total number of logs.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/administration/logs" verb="GET"

```json
{
  "data": [
    {
      "type": "log",
      "id": "42",
      "attributes": {
        "user_name": "Chandler Bing",
        "action": "magic_link_created",
        "parameters": null,
        "description": "Sent a magic link",
        "created_at": 1751284800,
        "updated_at": 1751284800
      },
      "links": {
        "self": "{{app.url}}/api/administration/logs/42"
      }
    }
  ],
  "links": {
    "first": "{{app.url}}/api/administration/logs?page=1",
    "last": "{{app.url}}/api/administration/logs?page=2",
    "prev": null,
    "next": "{{app.url}}/api/administration/logs?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "path": "{{app.url}}/api/administration/logs",
    "per_page": 10,
    "to": 10,
    "total": 15
  }
}
```

:::/code
:::/column
:::/section

:::section columns
:::column

## Get a specific log

:::description
This endpoint returns a specific log belonging to the user who owns the API key.

A `404 Not Found` response is returned when the log does not exist or belongs to another user.
:::/description

:::parameters title="URL parameters"
:::attribute name="log" type="integer" required
The ID of the log to return.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The log resource.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `log`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the log.
:::/attribute

:::attribute name="data.attributes" type="object"
The attributes of the log.
:::/attribute

:::attribute name="data.attributes.user_name" type="string"
The current name of the user, or their name when the log was created if the user no longer exists.
:::/attribute

:::attribute name="data.attributes.action" type="string"
The machine-readable name of the action.
:::/attribute

:::attribute name="data.attributes.parameters" type="object|null"
Values recorded for the action and used to produce its description.
:::/attribute

:::attribute name="data.attributes.description" type="string"
A human-readable description of the action.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the log was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer|null"
The time the log was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual log endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/administration/logs/{log}" verb="GET"

```json
{
  "data": {
    "type": "log",
    "id": "43",
    "attributes": {
      "user_name": "Chandler Bing",
      "action": "user_profile_updated",
      "parameters": null,
      "description": "Updated their personal profile",
      "created_at": 1751284800,
      "updated_at": 1751284800
    },
    "links": {
      "self": "{{app.url}}/api/administration/logs/43"
    }
  }
}
```

:::/code
:::/column
:::/section
