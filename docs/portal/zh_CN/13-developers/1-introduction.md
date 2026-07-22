---
id: developers.index
title: 开发者与 API
slug: kaifazhe-yu-api
section: kaifazhe-yu-api
---

# 开发者与 API

KolleK 提供了一套完整的 JSON API，与网页应用完全对应：你在应用里能做的事，用一个令牌和一个 HTTP 客户端同样能做到。本节帮助你完成身份验证并大致了解整体结构。完整的接口参考文档是从代码自动生成的，始终保持最新，因此本节页面不会重复其中的内容。

请按以下顺序阅读各页面：

1. @doc(api.overview)。API 涵盖哪些内容、整体结构，以及在哪里查看自动生成的参考文档。
2. @doc(api.authenticate)。获取令牌并发出你的第一个请求。
3. @doc(api.rateLimitsAndConventions)。分页、金额、限流以及错误处理方式。
4. @doc(webhooks.overview)。今天就注册接收端点，并了解目前哪些事件会触发（哪些还不会）。

如需查看每个接口的具体细节（参数、响应结构、示例），请前往你实例上的自动生成参考文档 `/docs/api`。
