---
id: selfHosting.setupEmailDelivery
title: 设置邮件发送
slug: shezhi-youjian-fasong
section: zi-tuoguan
---

# 设置邮件发送

邮件是 KolleK 在浏览器会话之外触达用户的方式：@doc(collaboration.invitePeople, "邀请")、@doc(auth.magicLinks, "魔法链接")、密码重置、邮箱验证，以及 @doc(security.alertEmails, "安全提醒") 都通过邮件发送。在你配置好邮件发送之前，这些邮件哪里都到不了。

## 默认设置不会发送任何邮件

全新安装的实例默认使用 `MAIL_MAILER=log`。每一封邮件都只会被写入应用日志文件，而不会真正发送出去。这是有意为之的设计：这样一来，一个配置未完成的实例不会悄悄用错误的地址发出邮件，而你在测试时也能准确看到本该发送的内容。

:::note
如果有人在一个新实例上反馈“我从没收到邀请邮件”，几乎总是因为这个默认设置。邮件其实存在，只是在日志文件里。参见 @doc(troubleshooting.emailDelivery)。
:::

发送真实邮件有两种受支持的方式：任意 SMTP 服务器，或者 Resend 服务。

## 方式一：SMTP

::::steps
:::step title="设置邮件驱动和服务器信息"
在 `.env` 中设置：

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

任何提供 SMTP 凭据的事务性邮件服务商或自建邮件服务器都可以使用。
:::

:::step title="设置发件人信息"
设置用户会看到的发件地址和名称：

```bash
MAIL_FROM_ADDRESS=kollek@yourdomain.com
MAIL_FROM_NAME="KolleK"
```

请使用一个你自己掌控、并且已经完成发信配置（在服务商处设置了 SPF 和 DKIM）的域名，否则邮件很可能会被归入垃圾邮件。
:::

:::step title="应用配置并测试"
重新创建容器，然后触发一封真实邮件，例如在登录页面请求一个魔法链接：

```bash
docker compose up -d
```
:::
::::

## 方式二：Resend

如果你使用 [Resend](https://resend.com)，设置：

```bash
USE_RESEND=true
RESEND_API_KEY=re_your_api_key
```

之后邮件会通过 Resend 的 API 发送，而不是通过 SMTP，并且每次发送都会记录对应的 Resend 消息 ID。

## 验证邮件发送是否生效

KolleK 会为每个用户记录它发送过的每一封邮件，包括主题、正文和投递状态。测试之后，检查以下两处：

- 你的收件箱，这个不用多说。
- 收件人个人资料中的**已发送邮件**页面，其中列出了实例发送给他们的所有邮件。参见 @doc(activity.logAndSentEmails, "你的个人活动日志与已发送邮件")。

常见的失败迹象：

- **什么都没收到，也没有报错。** 邮件驱动可能仍然是 `log`。检查是否已经通过重新创建容器让 `.env` 生效。
- **邮件发出去了，但进了垃圾邮件。** 发件域名没有完成身份验证，请在服务商处配置 SPF 和 DKIM。
- **日志中出现发送错误。** 凭据或服务器信息有误，queue 容器的日志中会包含服务商返回的错误信息。

邮件由后台队列发送，因此 **queue** 容器必须处于运行状态，实例才能真正发出任何邮件。

## 接下来去哪里

- 在 @doc(reference.emailsSent) 中了解你的实例会发送哪些邮件。
- 在 @doc(troubleshooting.emailDelivery) 中排查邮件发送问题。
