---
id: selfHosting.configure
title: 配置你的实例
slug: peizhi-ni-de-shili
section: zi-tuoguan
---

# 配置你的实例

你实例的所有配置都通过 @doc(selfHosting.installDocker, "安装过程") 中创建的 `.env` 文件完成。本页按用途分组，介绍运维者实际会用到的设置项，而不是逐一列出模板中的每一个变量。

修改 `.env` 之后，需要重新创建容器才能生效：

```bash
docker compose up -d
```

## 身份与 URL

- `APP_NAME` 是界面和邮件中显示的名称，默认值为 `Kollek`。
- `APP_URL` 是你实例的公开访问地址。邮件中的链接都基于它生成，因此必须设置成用户实际访问的地址。
- `APP_PORT` 是 web 容器对外暴露的主机端口，默认是 `8000`。

## 应用密钥

`APP_KEY` 用于加密静态敏感数据。你只在安装时设置一次，之后不要随意更改。这个设置足够重要，专门有 @doc(selfHosting.applicationKeyAndEncryption, "独立的一页") 来讲解，其中还介绍了 `APP_PREVIOUS_KEYS` 密钥轮换机制。

## 数据库

`DB_DATABASE`、`DB_USERNAME`、`DB_PASSWORD` 和 `DB_ROOT_PASSWORD` 用于配置内置的 MySQL 容器。在首次启动前，请将两个密码都从占位值改为真实密码。`RUN_MIGRATIONS` 控制 web 容器是否在启动时自动迁移数据库（默认是 `true`）。

## 邮件

`MAIL_MAILER` 决定邮件如何从你的实例发出，默认值是 `log`。

:::note
使用默认的 `log` 邮件驱动时，实际上不会发送任何邮件。邀请、魔法链接、密码重置和安全提醒都只会被写入应用日志。配置一个真正可用的邮件服务，几乎是每个实例都需要完成的设置。参见 @doc(selfHosting.setupEmailDelivery)。
:::

## 文件存储

`FILESYSTEM_DISK` 默认值为 `local`：上传的照片和文档存储在 `storage-data` 数据卷中。如果想改用兼容 S3 的对象存储，将其设置为 `s3`，并填写 `AWS_ACCESS_KEY_ID`、`AWS_SECRET_ACCESS_KEY`、`AWS_DEFAULT_REGION`、`AWS_BUCKET`，以及非 AWS 服务商所需的 `AWS_ENDPOINT` 变量。无论使用哪种方式，文件都通过带账户校验的私有路由提供给用户，而不会以公开 URL 的形式暴露。

## 日常维护

- `TRASH_RETENTION_DAYS` 决定软删除的对象在 @doc(dataSafety.restoreFromTrash, "回收站") 中保留多久，之后会被每晚的清理任务永久删除。默认值是 30 天。
- `ACCOUNT_DELETION_NOTIFICATION_EMAIL` 是当用户删除自己的账户，或被 @doc(users.inactiveDeletion, "非活跃用户清理") 移除时收到通知的邮箱地址。建议填写你自己的邮箱，这样有人离开时你不会毫无察觉。

## 公开的营销站点

`SHOW_MARKETING_SITE` 默认值为 `false`，也就是说你的实例只提供应用本身。将其设置为 `true` 后，还会提供公开的营销页面，以及 `/docs/api` 上自动生成的 API 参考文档。大多数私有实例都会关闭它，只有当你的开发者需要本地访问 API 参考文档时才建议开启。

## 不需要配置的部分

会话（`SESSION_DRIVER`）、缓存（`CACHE_STORE`）和队列（`QUEUE_CONNECTION`）默认都基于数据库存储。这些默认值对提供的服务栈来说已经是正确的，不需要额外添加 Redis 或其他服务。除非你清楚知道自己为什么要改，否则不要动它们。

## 接下来去哪里

- 在 @doc(selfHosting.setupEmailDelivery) 中让真正的邮件发送起来。
- 在 @doc(selfHosting.applicationKeyAndEncryption) 中了解你必须保护好的这个密钥。
- 设置好 @doc(selfHosting.backupAndRestore, "备份")。
