---
id: tutorials.selfHostWithDocker
title: "教程：使用 Docker 自托管 KolleK"
slug: shiyong-docker-zi-tuoguan-kollek
section: jiaocheng
---

# 教程：使用 Docker 自托管 KolleK

在本教程中，你会把一台什么都没有的机器，变成一个正在运行的 KolleK 实例：克隆项目、配置环境变量、生成应用密钥、启动整套服务、创建第一个账户，并授予第一个实例管理员权限。教程结束时，你会拥有一个可用的实例，也会知道更深入的运维指南从哪里开始阅读。

我们将跟随 Alex 一起操作，他正在自己的小型家庭服务器上，为收藏者俱乐部搭建一个实例。无论是在 VPS 上还是在笔记本电脑上，步骤都是一样的。

预计这个过程需要十五到三十分钟，其中大部分时间用在等待首次构建。

## 开始之前

你需要准备：

- 一台安装了 **Docker Engine 24 或更高版本**以及 **Compose 插件**（是 `docker compose` 命令，而不是较旧的 `docker-compose`）的机器。
- **Git**，用来克隆项目。
- 一个终端，以及在其中运行命令的基本能力。

先快速浏览一下 @doc(selfHosting.index, "自托管概述") 会很有帮助，因为它介绍了本教程会反复强调的一条原则：应用密钥只设置一次，之后绝不更改。

## 第一步：克隆项目并创建配置文件

```bash
git clone https://github.com/djaiss/beaver.git
cd beaver
cp .env.docker.example .env
```

`.env` 文件就是你实例的配置文件。运维者日常会用到的一切设置都在这里，@doc(selfHosting.configure, "配置指南") 会按分组逐一讲解。对于首次启动来说，只有接下来的两个步骤是必须完成的。

## 第二步：生成应用密钥

KolleK 使用一个只生成一次的密钥来加密静态敏感数据：

```bash
docker compose run --rm app php artisan key:generate --show
```

复制输出结果（以 `base64:` 开头），把它粘贴到 `.env` 中作为 `APP_KEY` 的值。

:::warning
应用密钥只设置一次，绝不能在正在运行的实例上更改。一旦密钥变化，所有加密内容（包括姓名、藏品和会话）都将永久无法读取。请把密钥的副本保存在安全的地方，因为数据库备份只有配合当初加密它的密钥才能恢复。
:::

包括如何有计划地进行密钥轮换在内的完整说明，参见 @doc(selfHosting.applicationKeyAndEncryption)。

## 第三步：检查密码和 URL

在编辑器中打开 `.env`，检查以下三项：

- **`DB_PASSWORD` 和 `DB_ROOT_PASSWORD`。** 两者默认都是占位值，请在首次启动前改成你自己的强密码，因为数据库正是在首次启动时用这两个密码创建的。
- **`APP_URL`。** 用户实际会输入的地址。Alex 为俱乐部网络设置的是 `http://server.local:8000`。默认值是 `http://localhost:8000`。
- **`APP_PORT`。** 对外发布的端口，除非你修改，否则默认是 `8000`。

## 第四步：启动整套服务

```bash
docker compose up -d --build
```

首次运行会构建镜像，需要几分钟时间。之后 Compose 会启动四个容器：

- **app**，Web 服务器。这是唯一执行数据库迁移的角色，因此数据库结构只会被创建一次。
- **queue**，负责发送邮件和处理后台任务的工作进程。
- **scheduler**，运行每日维护任务。
- **mysql**，数据库。

用 `docker compose ps` 检查所有容器是否都已启动。当 app 容器状态显示为健康时，在浏览器中打开你的 `APP_URL`，你应该会看到 KolleK 的登录页面。

## 第五步：创建第一个账户

前往注册页面完成注册。这个过程和任何普通用户完全一样，具体步骤在 @doc(accounts.create) 中，完成后你就会成为这个实例第一个账户的所有者。

Alex 完成了注册，进入了新手入门清单页面，并克制住了立刻开始编目的冲动，直到运维方面的工作全部完成。

## 第六步：授予第一个实例管理员权限

实例管理员可以通过实例管理面板查看实例上的所有账户。这个标志通过命令行授予：

```bash
docker compose exec app php artisan kollek:make-instance-administrator you@example.com
```

使用你刚刚注册时用的邮箱。同样的命令加上 `--revoke` 可以收回这个标志。这个标志具体做了什么、又刻意没有做什么，在 @doc(instanceAdmin.grantAccess) 中有说明。

## 成果如何

你现在拥有一个可用的实例：Web 应用在你的 URL 上响应请求，队列工作进程和调度器在旁边运行，数据存放在一个具名数据库卷中，而你自己既是账户所有者，也是实例管理员。俱乐部成员现在可以注册自己的账户，你也可以 @doc(tutorials.inviteHousehold, "邀请他们加入你的账户")。

## 松一口气之前还要做一件事

默认情况下，这个实例只会把待发送的邮件写入日志文件，而不会真正发送出去。在你配置好真正可用的邮件服务之前，邀请邮件、魔法链接和密码重置邮件都会悄无声息地发不出去。这是有意为之的设计，修复起来也很简单：参见 @doc(selfHosting.setupEmailDelivery)。

## 需要避免的常见错误

- **弄丢应用密钥。** 现在就把它单独备份好，和数据库分开存放。没有它，备份就只是一堆密文。
- **保留数据库的占位密码不改。** 一定要在首次启动之前修改，而不是之后。
- **跳过邮件配置。** 第一份“我从没收到邀请”的反馈，原因几乎总是这个。

## 接下来去哪里

- 在 @doc(selfHosting.configure) 中通读你刚才跳过的所有设置项。
- 在目录变得珍贵之前，先设置好 @doc(selfHosting.backupAndRestore, "备份")。
- 有新版本发布时，参照 @doc(selfHosting.upgrade) 进行升级。
