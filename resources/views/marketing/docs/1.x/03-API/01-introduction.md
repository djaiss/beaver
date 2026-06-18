# Introduction

:::markdown-actions url="{{docs.markdown_url}}"
:::copy-for-llm
:::/copy-for-llm

:::view-as-markdown url="{{docs.markdown_url}}"
:::/view-as-markdown
:::/markdown-actions

:::toc

- [Test the API yourself](#test-the-api-yourself)
- [Conventions of the API](#conventions-of-the-api)
- [Pagination](#pagination)
- [Health](#health)
  :::/toc

:::section columns divided
:::column
The {{app.name}} API is organized around REST. Our API has predictable resource-oriented URLs.

You **can not** use the {{app.name}} API in test mode. This means all requests will be processed towards your production account. Please be cautious.

The {{app.name}} API doesn’t support bulk updates. You can work on only one object per request.
:::/column

:::column
**Base URL**

:::code

```text
{{app.url}}/api
```

:::/code
:::/column
:::/section

:::section divided

## Test the API yourself

If you want to test the API yourself, we provide two convenient tools for you to use: [Bruno](https://www.usebruno.com/).

The documentation is included in the GitHub repository, under the [docs](https://github.com/djaiss/lifeOS/tree/main/docs) folder.

Why these tools? Because they're fresh, new, free and open source under the MIT license, and I really like their ethos.
:::/section

:::section divided

## Conventions of the API

There is no strict standard for JSON payloads, but we do try to follow [the JSON:API specification](https://jsonapi.org/), which defines a structured format for responses.
:::/section

:::section columns divided
:::column

## Pagination

All endpoints that return a collection of resources support pagination.

The default value for `per_page` is 10. This can not be changed at the moment.

All responses will include links to navigate to the next and previous pages.
:::/column

:::column
:::code title="Example of pagination" verb="GET"

```json
{
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "« Previous",
        "page": null,
        "active": false
      },
      {
        "url": "{{app.url}}/api/settings/logs?page=1",
        "label": "1",
        "page": 1,
        "active": true
      },
      {
        "url": null,
        "label": "Next »",
        "page": null,
        "active": false
      }
    ],
    "path": "{{app.url}}/api/settings/logs",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

:::/code
:::/column
:::/section

:::section columns
:::column

## Health

:::description
This endpoint checks the health of the application and returns a simple "ok" message. It lets you know if the application is running and if the database is connected.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Query parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="message" type="string"
The message of the response.
:::/attribute

:::attribute name="services" type="object"
The status of the application's services.
:::/attribute

:::attribute name="services.database" type="string"
The status of the database connection.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/health" verb="GET"

```json
{
  "message": "ok",
  "services": {
    "database": "up"
  }
}
```

:::/code
:::/column
:::/section
