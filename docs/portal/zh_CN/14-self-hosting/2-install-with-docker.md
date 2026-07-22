---
id: selfHosting.installDocker
title: 使用 Docker 安装
slug: shiyong-docker-anzhuang
section: zi-tuoguan
---

# 使用 Docker 安装

这是最权威的安装指南，会带你从一台安装了 Docker 的机器，一步步搭建出一个正在运行的 KolleK 实例，并创建你的第一个账户。整个过程大约需要十五分钟。

代码仓库中的 `docker/README.md` 从运维者的角度记录了同样的流程，并且始终与代码保持同步。如果本页内容与该文件有出入，请以 `docker/README.md` 为准。

## 开始之前

你需要准备：

- 一台安装了 **Docker Engine 24 或更高版本**以及 **Compose 插件**（`docker compose`）的机器。
- 一份 KolleK 代码仓库，克隆或下载均可。
- 花十分钟专心处理环境变量文件。真正重要的失误往往就出在这一步。

除此之外不需要别的。整套服务自带 MySQL 数据库，会话、缓存和队列都基于数据库存储，因此不需要安装 Redis。

## 安装

::::steps
:::step title="创建环境变量文件"
在仓库根目录下，复制 Docker 环境变量模板：

```bash
cp .env.docker.example .env
```

这个文件驱动着整套服务，你将在接下来的两个步骤中编辑它。
:::

:::step title="生成应用密钥"
生成一个密钥并复制输出结果：

```bash
docker compose run --rm app php artisan key:generate --show
```

将打印出的值粘贴到 `.env` 中的 `APP_KEY`。这个密钥用于加密你的静态数据。**现在就设置好它，之后绝不要更改。** 一旦密钥变化，所有加密字段和所有会话都会永久无法读取。如果你还没读过，请先阅读 @doc(selfHosting.applicationKeyAndEncryption) 再继续。
:::

:::step title="检查密码和 URL"
在 `.env` 中，将 `DB_PASSWORD` 和 `DB_ROOT_PASSWORD` 从占位值改为真实密码，并将 `APP_URL` 设置为用户实际访问的地址。默认值是 `http://localhost:8000`，用于在自己机器上首次尝试是没问题的。
:::

:::step title="启动整套服务"
构建并启动所有服务：

```bash
docker compose up -d --build
```

首次构建需要几分钟。构建完成后，web 容器会自动执行数据库迁移，实例随即在你设置的 `APP_URL` 上启动。
:::

:::step title="创建你的第一个账户"
在浏览器中打开该 URL，通过注册页面完成注册。这会创建你的个人用户和你的第一个账户，具体过程与 @doc(accounts.create) 中描述的一致。

::screenshot{label="全新安装实例的注册页面"}
:::

:::step title="为自己授予实例管理员权限"
如果你想使用服务器级的管理面板，可以为自己的用户授予相应标志：

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

关于这个操作具体授予了什么权限、又没有授予什么权限，参见 @doc(instanceAdmin.grantAccess)。
:::
::::

## 实际运行的内容

Compose 服务栈会启动四个容器。其中三个运行相同的 KolleK 镜像，通过 `CONTAINER_ROLE` 环境变量区分不同角色：

- **app** 通过 nginx 和 PHP 提供 Web 应用服务，是唯一会执行数据库迁移的容器，在启动时自动执行。
- **queue** 处理来自 `high`、`default` 和 `low` 队列的后台任务（邮件、投递、日志记录）。
- **scheduler** 触发 @doc(selfHosting.scheduledJobs) 中描述的每日维护任务。

第四个容器是 **mysql**，运行 MySQL 8.4。

你的数据存放在两个独立于容器的具名 Docker 卷中：`db-data` 用于数据库，`storage-data` 用于存放上传的照片和文档。容器可以随意重建或替换，数据卷始终保留。

:::note
三个应用容器必须共享同一份 `.env`，尤其是同一个 `APP_KEY`。Compose 文件已经默认这样配置，如果你要自定义设置，请保持这一点不变。
:::

## 如果你想自己手动执行迁移

默认情况下，web 容器每次启动都会自动迁移数据库，让升级过程无需人工干预。如果你想手动掌控迁移过程，可以在 `.env` 中设置 `RUN_MIGRATIONS=false`，然后在需要时自行运行迁移：

```bash
docker compose exec app php artisan migrate --force
```

## 接下来去哪里

- 通读 @doc(selfHosting.configure)，了解 `.env` 还能控制哪些内容。
- 在 @doc(selfHosting.setupEmailDelivery) 中让邮件真正能够发送。在此之前，邀请邮件和登录链接只会被写入日志文件，而不会进入收件箱。
- 在录入真实数据之前，先设置好 @doc(selfHosting.backupAndRestore, "备份")。
