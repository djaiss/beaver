# Emails

:::markdown-actions url="{{docs.markdown_url}}"
:::copy-for-llm
:::/copy-for-llm

:::view-as-markdown url="{{docs.markdown_url}}"
:::/view-as-markdown
:::/markdown-actions

:::toc

- [List the logged-in user's emails](#list-the-logged-in-users-emails)
- [Get a specific email](#get-a-specific-email)
  :::/toc

:::section columns divided
:::column
The emails endpoints let you review the emails that have been sent to the logged-in user.

Both endpoints require authentication. A user can only access their own emails. Emails are returned newest first, ordered by the time they were sent.
:::/column

:::column
:::code title="Endpoints"

```text
GET /api/administration/emails
GET /api/administration/emails/{email}
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## List the logged-in user's emails

:::description
This endpoint returns the emails that have been sent to the user who owns the API key.

The response is paginated with 10 emails per page.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Query parameters"
:::attribute name="page" type="integer"
The page number to return. The first page is returned when this parameter is omitted.
:::/attribute

:::attribute name="per_page" type="integer"
The number of emails to return per page. The default is 10, the minimum is 1 and the maximum is 100.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="array"
The emails on the current page.
:::/attribute

:::attribute name="data[].type" type="string"
The type of the resource. This value is always `email`.
:::/attribute

:::attribute name="data[].id" type="string"
The ID of the email.
:::/attribute

:::attribute name="data[].attributes" type="object"
The attributes of the email.
:::/attribute

:::attribute name="data[].attributes.email_type" type="string"
The machine-readable type of the email.
:::/attribute

:::attribute name="data[].attributes.email_address" type="string"
The recipient email address.
:::/attribute

:::attribute name="data[].attributes.subject" type="string|null"
The subject line of the email.
:::/attribute

:::attribute name="data[].attributes.body" type="string|null"
The body content of the email.
:::/attribute

:::attribute name="data[].attributes.sent_at" type="integer|null"
The time the email was sent, as a Unix timestamp, or `null` when it has not been sent.
:::/attribute

:::attribute name="data[].attributes.delivered_at" type="integer|null"
The time the email was delivered, as a Unix timestamp, or `null` when it has not been delivered.
:::/attribute

:::attribute name="data[].attributes.bounced_at" type="integer|null"
The time the email bounced, as a Unix timestamp, or `null` when it did not bounce.
:::/attribute

:::attribute name="data[].attributes.created_at" type="integer"
The time the email record was created, as a Unix timestamp.
:::/attribute

:::attribute name="data[].attributes.updated_at" type="integer|null"
The time the email record was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data[].links.self" type="string"
The URL of the individual email endpoint.
:::/attribute

:::attribute name="links" type="object"
URLs for navigating between pages.
:::/attribute

:::attribute name="meta" type="object"
Pagination information, including the current page, page size, and total number of emails.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/administration/emails" verb="GET"

```json
{
  "data": [
    {
      "type": "email",
      "id": "42",
      "attributes": {
        "email_type": "welcome",
        "email_address": "chandler.bing@friends.test",
        "subject": "Welcome to LifeOS",
        "body": "Could this BE any more of a welcome email?",
        "sent_at": 1751284800,
        "delivered_at": 1751284800,
        "bounced_at": null,
        "created_at": 1751284800,
        "updated_at": 1751284800
      },
      "links": {
        "self": "{{app.url}}/api/administration/emails/42"
      }
    }
  ],
  "links": {
    "first": "{{app.url}}/api/administration/emails?page=1",
    "last": "{{app.url}}/api/administration/emails?page=2",
    "prev": null,
    "next": "{{app.url}}/api/administration/emails?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "path": "{{app.url}}/api/administration/emails",
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

## Get a specific email

:::description
This endpoint returns a specific email belonging to the user who owns the API key.

A `404 Not Found` response is returned when the email does not exist or belongs to another user.
:::/description

:::parameters title="URL parameters"
:::attribute name="email" type="integer" required
The ID of the email to return.
:::/attribute
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The email resource.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `email`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the email.
:::/attribute

:::attribute name="data.attributes" type="object"
The attributes of the email.
:::/attribute

:::attribute name="data.attributes.email_type" type="string"
The machine-readable type of the email.
:::/attribute

:::attribute name="data.attributes.email_address" type="string"
The recipient email address.
:::/attribute

:::attribute name="data.attributes.subject" type="string|null"
The subject line of the email.
:::/attribute

:::attribute name="data.attributes.body" type="string|null"
The body content of the email.
:::/attribute

:::attribute name="data.attributes.sent_at" type="integer|null"
The time the email was sent, as a Unix timestamp, or `null` when it has not been sent.
:::/attribute

:::attribute name="data.attributes.delivered_at" type="integer|null"
The time the email was delivered, as a Unix timestamp, or `null` when it has not been delivered.
:::/attribute

:::attribute name="data.attributes.bounced_at" type="integer|null"
The time the email bounced, as a Unix timestamp, or `null` when it did not bounce.
:::/attribute

:::attribute name="data.attributes.created_at" type="integer"
The time the email record was created, as a Unix timestamp.
:::/attribute

:::attribute name="data.attributes.updated_at" type="integer|null"
The time the email record was last updated, as a Unix timestamp.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the individual email endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/administration/emails/{email}" verb="GET"

```json
{
  "data": {
    "type": "email",
    "id": "43",
    "attributes": {
      "email_type": "welcome",
      "email_address": "monica.geller@friends.test",
      "subject": "Welcome to LifeOS",
      "body": "I know!",
      "sent_at": 1751284800,
      "delivered_at": 1751284800,
      "bounced_at": null,
      "created_at": 1751284800,
      "updated_at": 1751284800
    },
    "links": {
      "self": "{{app.url}}/api/administration/emails/43"
    }
  }
}
```

:::/code
:::/column
:::/section
