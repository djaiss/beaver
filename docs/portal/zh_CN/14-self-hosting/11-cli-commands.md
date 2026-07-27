---
id: selfHosting.cliCommands
title: 使用命令行管理
slug: shiyong-mingling-hang-guanli
section: zi-tuoguan
---

# 使用命令行管理

有一些运维任务只能在命令行中完成，而不是在 Web 应用里。本页列出了运行实例时你实际可能用到的 artisan 命令，并为每个命令指向更详细的说明页面。

在 Docker 安装方式下，所有命令都要通过 web 容器执行：

```
docker compose exec app php artisan <command>
```

## 日常运维

### 授予或撤销实例管理员权限

```
php artisan kollek:make-instance-administrator you@example.com
php artisan kollek:make-instance-administrator you@example.com --revoke
```

为指定邮箱对应的用户授予（或收回）服务器级管理员标志。安装完成后，第一个管理员就是通过这种方式初始化的。参见 @doc(instanceAdmin.grantAccess)。

### 创建 Webhook 端点

```
php artisan kollek:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

为某个用户注册一个 Webhook 端点，并打印出它的 ID 和签名密钥。用户也可以自行在个人资料设置中完成这个操作。请注意，目前应用内还没有任何事件会触发 Webhook，参见 @doc(webhooks.overview)。

### 重建照片搜索索引

```
php artisan photos:rebuild-search-index
```

重建照片库背后的搜索索引，并补全缺失的图片尺寸信息。升级到引入照片页面的版本之后，运行一次即可。之后随时重复运行也是安全的，它会跳过文件缺失的照片，不会更改其他任何内容。参见 @doc(selfHosting.upgrade)。

### 重建账户搜索索引

```
php artisan search:rebuild-index
```

重建账户范围搜索背后的索引，涵盖藏品、收藏、副本、照片、借出记录、存放位置、套组、系列、分类、标签和文档。升级到引入搜索功能的版本之后，运行一次：迁移只创建数据表，填充数据的只有这条命令。加上 `--type=item` 可以只重建某一种记录。之后随时重复运行也是安全的，索引出现偏差时同样用它修复。参见 @doc(search.overview)。

### 清除 Cloudflare 缓存

```
php artisan kollek:purge-cloudflare-cache
```

丢弃 Cloudflare 保存的所有公开页面，这些页面本来会从它的缓存中提供七天。不会丢失任何东西：每个页面会在下一次访问时重新渲染。该命令需要 `CLOUDFLARE_API_TOKEN` 和 `CLOUDFLARE_ZONE_ID`，缺少时会明确提示。面板里也有同样的按钮，详见 @doc(instanceAdmin.panel)。

### 为翻译搭建语言包骨架

```
php artisan kollek:localize fr_FR
```

提取应用中所有可翻译的字符串，并将其同步到 `lang/` 目录下对应语言的 JSON 文件中。参见 @doc(selfHosting.addLanguage)。

## 仅用于开发环境

代码库中还有另外两个命令，它们都不应该出现在生产实例上。`kollek:bruno` 会用种子数据重置数据库，用于 API 客户端测试，这会破坏真实数据；`kollek:sync-skills` 用于维护项目自身的工具链。作为运维者，这两个命令你都可以忽略。

:::warning
切勿在真实实例上运行 `kollek:bruno`。它会清空数据库，并重新填充演示数据。
:::

## 接下来去哪里

- 在 @doc(instanceAdmin.grantAccess) 中初始化你的管理员。
- 在 @doc(selfHosting.upgrade) 中让实例保持最新。
- 在 @doc(selfHosting.addLanguage) 中翻译界面。
