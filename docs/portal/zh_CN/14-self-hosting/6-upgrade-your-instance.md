---
id: selfHosting.upgrade
title: 升级你的实例
slug: shengji-ni-de-shili
section: zi-tuoguan
---

# 升级你的实例

升级 KolleK 的设计目标就是“无聊”：拉取新版本，重新构建，完成。本页解释这为什么是安全的，以及升级之后你需要知道的一个额外步骤。

## 为什么升级不会丢失数据

有两个特性保证了升级路径的安全：

- **你的数据存放在具名数据卷中**（数据库使用 `db-data`，文件使用 `storage-data`），独立于容器和镜像存在。重建容器不会影响它们。
- **迁移只会向前推进。** web 容器在启动时会通过 `migrate --force` 应用所有待处理的数据库迁移，而 KolleK 从不会发布重置数据或破坏性重写数据的迁移。升级只会为你的数据库结构做增量修改。

## 升级步骤

::::steps
:::step title="先做好备份"
按照 @doc(selfHosting.backupAndRestore) 中的说明，导出数据库并打包存储卷。升级过程在设计上是安全的，但一份备份能让“设计上安全”变成“绝对安全”。
:::

:::step title="获取新版本"
在代码仓库目录下，拉取你要升级到的版本：

```bash
git pull
```
:::

:::step title="重新构建并重启"
```bash
docker compose up -d --build
```

Compose 会重新构建镜像并重建容器。启动时，web 容器会自动应用所有新的迁移，之后实例就会在你的 `APP_URL` 上重新运行。
:::
::::

如果你希望手动掌控迁移过程，可以设置 `RUN_MIGRATIONS=false`，并在升级流程中自行执行 `docker compose exec app php artisan migrate --force`，具体做法参见 @doc(selfHosting.installDocker)。

## 照片搜索索引这一步

有一次升级包含了一项一次性维护任务：早于照片库页面上线的实例，需要为已有照片手动构建一次照片搜索索引，否则这些照片的搜索结果会一直是空的。

```bash
docker compose exec app php artisan photos:rebuild-search-index
```

这个命令是幂等的，在任何实例上运行都是安全的，如果不确定就直接运行它。它还会为那些在记录图片尺寸功能上线之前上传的照片补全尺寸信息。

:::note
升级过程中不要更改 `APP_KEY`。这个密钥的生命周期跨越所有版本。如果某份升级指南看起来要求你更换密钥，那一定是你理解有误。参见 @doc(selfHosting.applicationKeyAndEncryption)。
:::

## 接下来去哪里

- 保持 @doc(selfHosting.backupAndRestore, "备份") 是最新的，让每一次升级都有备份可依。
- 了解 @doc(selfHosting.scheduledJobs)，这些任务会在 scheduler 容器恢复运行后自动继续执行。
