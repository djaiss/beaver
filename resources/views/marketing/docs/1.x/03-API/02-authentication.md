# Authentication

:::section columns divided
:::column
The {{app.name}} API uses API keys to authenticate requests. You can view and manage your API keys in the settings section of your account.

Your API keys carry many privileges, so be sure to keep them secure. Do not share your secret API keys in publicly accessible areas such as GitHub or client-side code.

On our instance, all API requests must be made over HTTPS. Calls made over plain HTTP will fail. API requests without authentication will also fail, except for the health and login endpoints. On your instance, it will be up to you.

You must send the API key in the `Authorization` header. The value must be `Bearer`, followed by a space and then the API key.

Authenticated API requests on our instance are limited to 60 requests per minute. On your instance, you can change this setting in the [API routes configuration](https://github.com/djaiss/lifeOS/blob/main/routes/api.php).

There are two ways to get an API key:

- Create an API key in the settings section of your account.
- Use the login API route described below. Logging in with your email and password returns an API key that you can use to authenticate subsequent requests.
  :::/column

:::column
:::code title="Authenticated request"

```bash
curl -X GET "{{app.url}}/api/vaults" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

:::/code
:::/column
:::/section

:::section columns divided
:::column

## Login

:::description
This endpoint logs in a user and returns an API key. This is the only endpoint that lets you use your email and password to authenticate API requests.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Query parameters"
:::attribute name="email" type="string" required
The email address of the user. Maximum 255 characters.
:::/attribute

:::attribute name="password" type="string" required
The password of the user. Maximum 255 characters.
:::/attribute
:::/parameters

:::parameters title="Response attributes"
:::attribute name="message" type="string"
The message of the response.
:::/attribute

:::attribute name="status" type="integer"
The HTTP status code of the response.
:::/attribute

:::attribute name="data" type="object"
The data of the response.
:::/attribute

:::attribute name="data.token" type="string"
The API key of the user.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/login" verb="POST"

```json
{
  "message": "Authenticated",
  "status": 200,
  "data": {
    "token": "1|1234567890"
  }
}
```

:::/code
:::/column
:::/section

:::section columns
:::column

## Logout

:::description
This endpoint logs out a user and **deletes the API key that was used to authenticate the request**. Please be certain.
:::/description

:::parameters title="URL parameters" empty="This endpoint does not have any parameters."
:::/parameters

:::parameters title="Query parameters" empty="No query parameters are available for this endpoint."
:::/parameters

:::parameters title="Response attributes"
:::attribute name="message" type="string"
The message of the response.
:::/attribute

:::attribute name="status" type="integer"
The HTTP status code of the response.
:::/attribute
:::/parameters
:::/column

:::column
:::code title="/api/logout" verb="DELETE"

```json
{
  "message": "Logged out successfully",
  "status": 200
}
```

:::/code
:::/column
:::/section
