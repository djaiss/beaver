---
id: api.authenticate
title: 使用 API 进行身份验证
slug: shiyong-api-jinxing-shenfen-yanzheng
section: kaifazhe-yu-api
---

# 使用 API 进行身份验证

每个 API 请求都通过一个 bearer 令牌进行身份验证。本页带你从零开始，完成第一个成功的请求，然后介绍如何通过 API 本身获取和撤销令牌。

请将示例中的 `https://kollek.example.com` 替换为你实例的实际地址。API 位于该地址下的 `/api` 路径。

## 最快的方式：在应用中创建密钥

获取令牌最简单的方式，是在你的个人资料中创建一个 API 密钥。

::::steps
:::step title="创建一个 API 密钥"
在应用中打开个人资料设置，进入 **API 密钥** 页面。创建一个密钥，并为它取一个之后能认出的标签，例如"报表脚本"。

::screenshot{label="个人资料设置中的 API 密钥页面，含新建密钥表单"}
:::

:::step title="复制令牌"
令牌只会在创建后显示一次。请立即复制它，并保存在安全的地方，例如密码管理器。如果丢失了，撤销该密钥并重新创建一个即可。
:::

:::step title="发出你的第一个请求"
在 `Authorization` 请求头中携带该令牌。一个不错的首次调用是 `/api/me`，它会返回你自己的用户信息：

```bash
curl https://kollek.example.com/api/me \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```
:::
::::

如果你收到了一份描述你用户信息的 JSON 文档，说明身份验证成功了。如何创建和撤销密钥，以及查看每个密钥最后一次使用的时间，详见 @doc(apiKeys.manage)。

:::note
令牌不会自动过期，会一直有效直到你手动撤销，因此请像对待密码一样对待令牌。
:::

## 通过 API 获取令牌

你也可以完全通过 HTTP 完成身份验证，这适合那些自行管理凭证的脚本和集成程序。

使用你的邮箱和密码登录以获取令牌：

```bash
curl -X POST https://kollek.example.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "you@example.com",
    "password": "your-password",
    "device_name": "Reporting script"
  }'
```

响应中的 `data.token` 字段就是你的令牌。可选的 `device_name` 用于为令牌命名，方便你之后在密钥列表中识别它。

有两点需要注意：

- 如果你的用户开启了@doc(security.twoFactorAuth, "两步验证")，登录接口还会要求提供一个 `code` 字段，内容为身份验证器应用当前显示的 TOTP 验证码，或者你的@doc(security.recoveryCodes, "恢复代码")之一。
- 通过 API 注册同样可行：`POST /api/register` 会创建一个带有自己账户的用户，并返回一个令牌，效果与在浏览器中注册完全一样。

这两个接口都限制为每分钟 6 次请求，这个额度足够正常登录使用，同时能防止暴力破解尝试。

## 撤销令牌

你有两种方式：

- `DELETE /api/logout` 会撤销发起该请求所用的令牌。当一个使用临时令牌的脚本运行结束时，可以使用这个方式。
- 个人资料中的 **API 密钥** 页面列出了所有令牌，可以撤销其中任意一个。自动生成的参考文档中对应的 API 密钥接口也能通过 HTTP 完成同样的操作。

无论密钥是在应用中创建还是删除，KolleK 都会向你发送邮件通知，因此意外的密钥活动不会被忽略。参见 @doc(security.alertEmails)。

## 接下来去哪里

- 在 @doc(api.rateLimitsAndConventions) 中了解请求的相关约定。
- 在 @doc(apiKeys.manage) 中管理你的令牌。
- 在 `/docs/api` 的自动生成参考文档中探索每一个接口。
