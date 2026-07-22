---
id: security.index
title: 安全概览
slug: anquan-gaikuang
section: anquan-gaikuang
---

# 安全概览

KolleK 保存着对你来说很重要的记录：你拥有什么、它值多少钱、存放在哪里。本页梳理了保护你的用户和数据的各项控制手段，方便你决定要开启哪些。它们全部都是可选的，其中大多数只需要你花五分钟。

## 你的密码

每个账户一开始都要设置密码。设置密码时 KolleK 会强制执行两条规则：密码至少要有八个字符，并且会与已知在历史数据泄露中出现过的密码列表进行比对。如果你尝试的密码被拒绝，那是因为它出现在了那些列表中，请换一个你没有在别处用过的密码。

你可以随时修改密码，忘记密码时也可以找回访问权限，见 @doc(auth.resetPassword)。

## 两步验证

你能做的最有价值的一项升级。开启两步验证后，用密码登录时还会要求你输入手机上认证器应用生成的六位数验证码，仅凭一个被窃取的密码已经不足以登录了。

在 @doc(security.twoFactorAuth) 中设置它，并在依赖它之前务必了解 @doc(security.recoveryCodes, "恢复代码")。

## 恢复代码

开启两步验证时，KolleK 会给你八个恢复代码。每个代码只能使用一次，可以代替认证器验证码，在你丢失手机时帮你重新登录。请把它们保存在安全的地方，@doc(security.recoveryCodes) 说明了具体方法。

## 魔法链接

一种无需密码的登录方式。KolleK 会给你发一封邮件，里面的链接可以直接登录，有效期为五分钟。这种方式很方便，但有一点权衡值得了解：魔法链接不会要求两步验证码，因为能访问你的邮箱本身就已经充当了第二重验证。@doc(auth.magicLinks) 介绍了什么时候适合使用它。

## API 密钥

如果你使用 KolleK 的 API，会用个人 API 密钥来进行身份验证。密钥在你的个人资料中创建和撤销，每当有密钥被创建或删除时，KolleK 都会给你发邮件，所以任何一个不是你自己创建的密钥都不会被忽略。见 @doc(apiKeys.manage)。

## 提醒邮件

KolleK 会监控一些值得告知你的事件：一次失败的登录尝试、一次来自新设备的登录、IP 地址的变化、API 密钥的创建或删除。这些事件发生时，你会收到一封邮件。@doc(security.alertEmails) 解释了每种提醒各自意味着什么，以及该如何应对。

## 一套合理的配置

如果你只打算做两件事，那就做这两件：

1. 开启 @doc(security.twoFactorAuth, "两步验证")。
2. 把你的 @doc(security.recoveryCodes, "恢复代码") 保存在手机以外的地方。

本节其余的内容，可以等你真正需要时再处理。

## 本节包含的页面

1. @doc(security.twoFactorAuth)
2. @doc(security.recoveryCodes)
3. @doc(auth.magicLinks)
4. @doc(auth.resetPassword)
5. @doc(security.alertEmails)
6. @doc(apiKeys.manage)
