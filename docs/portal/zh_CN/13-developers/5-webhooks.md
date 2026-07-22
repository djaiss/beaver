---
id: webhooks.overview
title: Webhook
slug: webhook
section: kaifazhe-yu-api
---

# Webhook

Webhook 让外部系统能够在你账户中发生某些事情时，接收到来自 KolleK 的 HTTP 调用。你现在就可以设置它，本页会说明具体方法。但请先阅读下面这段说明，因为它定下了本页其余内容的基调。

:::note
目前还没有任何应用事件会触发 Webhook。注册、签名和投递的相关机制都已经就绪并经过测试，但事件只会随着收藏领域功能的扩展而逐步开始触发。如果你愿意，现在就可以设置好接收端；只是暂时不要指望它能收到任何事件。@doc(troubleshooting.featureStatus, "功能状态页面")会追踪这一情况何时改变。
:::

## 目前已具备的功能

注册一个端点会保存一个目标 URL，以及它自己的签名密钥。将来当 KolleK 真正触发事件时，每个事件都会投递给你注册的每一个启用中的端点，并附带签名，方便你的接收端验证它确实来自你的实例。

Webhook 端点归属于你的用户，而不是整个账户。

## 注册一个端点

在应用中打开个人资料设置，进入 **Webhook** 页面。添加你的接收端所监听的 URL，并填写一个标签方便你记住它的用途。每个端点都会生成一个专属的签名密钥，即端点创建时生成的一个 64 位字符串。请将它妥善保存在你的接收端。

运营者也可以通过命令行创建端点：

```bash
php artisan kollek:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

该命令会输出端点 id 及其签名密钥。

## 你的接收端应期望收到的负载

每次投递都是一个 JSON `POST` 请求，结构如下：

```json
{
  "event": "example.event",
  "happened_at": "2026-07-20T14:30:00+00:00",
  "data": {}
}
```

- `event` 说明发生了什么，目前尚未定义任何具体事件名称。
- `happened_at` 是事件发生时间的 ISO 8601 时间戳。
- `data` 携带该事件的具体数据。

## 验证签名

每次投递都包含一个 `Signature` 请求头：这是使用你端点的签名密钥，对原始请求体计算得到的 HMAC SHA256 哈希值。请在你这一侧重新计算相同的哈希并进行比对。如果两者不一致，就丢弃该请求，因为它并非来自你的实例。

```php
$computed = hash_hmac('sha256', $rawRequestBody, $signingSecret);

if (! hash_equals($computed, $request->header('Signature'))) {
    abort(401);
}
```

## 投递与重试

投递任务会进入队列，在后台发送。投递失败时会以指数退避的方式重试，最多重试 3 次。你的接收端应尽快返回 2xx 状态码，再异步执行实际的处理逻辑。

在自托管实例上，投递任务运行在队列工作进程中，因此必须保持 queue 角色的容器运行。参见 @doc(selfHosting.installDocker)。

## 接下来去哪里

- 前往@doc(troubleshooting.featureStatus, "功能状态页面")查看目前哪些功能已经上线，哪些仍在等待中。
- 与此同时，可以从 @doc(api.authenticate) 开始，先针对 API 进行开发。
