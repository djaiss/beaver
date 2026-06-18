# Profile

:::markdown-actions url="{{docs.markdown_url}}"
:::copy-for-llm
:::/copy-for-llm

:::view-as-markdown url="{{docs.markdown_url}}"
:::/view-as-markdown
:::/markdown-actions

:::toc

- [Get the logged-in user's profile](#get-the-logged-in-users-profile)
- [Update the logged-in user's profile](#update-the-logged-in-users-profile)
  :::/toc

:::section columns divided
:::column
The profile endpoints let you retrieve and update the logged-in user's profile.

Both endpoints require authentication. The API key used for the request determines which user's profile is returned or updated.
:::/column

:::column
:::code title="Endpoints"

```text
GET /api/me
PUT /api/me
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Get the logged-in user's profile

:::description
This endpoint returns the profile of the user associated with the API key.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The user resource.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `user`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the user.
:::/attribute

:::attribute name="data.attributes" type="object"
The profile attributes of the user.
:::/attribute

:::attribute name="data.attributes.first_name" type="string"
The first name of the user.
:::/attribute

:::attribute name="data.attributes.last_name" type="string"
The last name of the user.
:::/attribute

:::attribute name="data.attributes.nickname" type="string|null"
The nickname of the user.
:::/attribute

:::attribute name="data.attributes.email" type="string"
The email address of the user.
:::/attribute

:::attribute name="data.attributes.locale" type="string"
The locale used by the user.
:::/attribute

:::attribute name="data.attributes.time_format_24h" type="boolean"
Whether the user prefers times displayed in 24-hour format.
:::/attribute

:::attribute name="data.links" type="object"
Links related to the user.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the user's profile endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/me" verb="GET"

```json
{
  "data": {
    "type": "user",
    "id": "1",
    "attributes": {
      "first_name": "Dwight",
      "last_name": "Schrute",
      "nickname": "Dwight",
      "email": "dwight.schrute@dundermifflin.com",
      "locale": "en",
      "time_format_24h": true
    },
    "links": {
      "self": "{{app.url}}/api/me"
    }
  }
}
```

:::/code
:::/column
:::/section

:::section columns
:::column

## Update the logged-in user's profile

:::description
This endpoint updates the profile of the user associated with the API key.

If the email address changes, the user must verify the new address before accessing the account again. Passwords cannot be changed through this endpoint.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Query parameters"
:::attribute name="first_name" type="string" required
The first name of the user. Maximum 100 characters.
:::/attribute

:::attribute name="last_name" type="string" required
The last name of the user. Maximum 100 characters.
:::/attribute

:::attribute name="nickname" type="string|null"
The nickname of the user. Maximum 100 characters.
:::/attribute

:::attribute name="email" type="string" required
The email address of the user. It must be valid, unique on the instance, and cannot use a disposable email provider. Maximum 255 characters.
:::/attribute

:::attribute name="locale" type="string" required
The user's locale. Accepted values are `en`, `fr_FR`, `es_ES`, and `de_DE`.
:::/attribute

:::attribute name="time_format_24h" type="string" required
Whether times should use the 24-hour format. Accepted values are `true` and `false`.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="data" type="object"
The updated user resource.
:::/attribute

:::attribute name="data.type" type="string"
The type of the resource. This value is always `user`.
:::/attribute

:::attribute name="data.id" type="string"
The ID of the user.
:::/attribute

:::attribute name="data.attributes" type="object"
The updated profile attributes of the user.
:::/attribute

:::attribute name="data.attributes.first_name" type="string"
The first name of the user.
:::/attribute

:::attribute name="data.attributes.last_name" type="string"
The last name of the user.
:::/attribute

:::attribute name="data.attributes.nickname" type="string|null"
The nickname of the user.
:::/attribute

:::attribute name="data.attributes.email" type="string"
The email address of the user.
:::/attribute

:::attribute name="data.attributes.locale" type="string"
The locale used by the user.
:::/attribute

:::attribute name="data.attributes.time_format_24h" type="boolean"
Whether the user prefers times displayed in 24-hour format.
:::/attribute

:::attribute name="data.links" type="object"
Links related to the user.
:::/attribute

:::attribute name="data.links.self" type="string"
The URL of the user's profile endpoint.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/me" verb="PUT"

```json
{
  "data": {
    "type": "user",
    "id": "1",
    "attributes": {
      "first_name": "Michael",
      "last_name": "Scott",
      "nickname": "Michael",
      "email": "michael.scott@dundermifflin.com",
      "locale": "fr_FR",
      "time_format_24h": true
    },
    "links": {
      "self": "{{app.url}}/api/me"
    }
  }
}
```

:::/code
:::/column
:::/section
