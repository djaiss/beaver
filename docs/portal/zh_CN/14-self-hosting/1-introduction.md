---
id: selfHosting.index
title: 自托管概述
slug: zi-tuoguan
section: zi-tuoguan
---

# 自托管概述

自行运行 KolleK 实例是官方支持的一种使用方式，而且完全免费。在你安装任何东西之前，本页会先告诉你这意味着什么，并给你一条比其他任何规则都重要的原则。

如果你还没决定是自托管还是使用托管实例，请先阅读 @doc(kollek.hostingOptions)。

## 运行一个实例意味着什么

KolleK 以单个 Docker 镜像的形式发布，通过环境变量选择三种不同的角色：

- **web** 角色负责提供应用本身。
- **queue** 角色处理后台任务（发送邮件、投递 Webhook、记录日志）。
- **scheduler** 角色运行每日维护任务。

项目提供的 Docker Compose 文件会同时启动这三个角色，以及一个 MySQL 数据库。会话、缓存和队列都基于数据库存储，因此不需要额外运行 Redis 或其他服务。上传的照片和文档存储在一个存储卷中，默认使用本地磁盘，也可以配置为使用兼容 S3 的存储服务。

硬件要求不高：只需要一台安装了 Docker Engine 24 或更高版本以及 Compose 插件的机器。一台小型虚拟服务器就足以轻松运行个人实例。

## 现在就要牢记的一条原则

KolleK 使用你实例的应用密钥对静态敏感数据进行加密。

:::warning
应用密钥只能在首次启动前设置一次，之后绝不能在正在运行的实例上更改。一旦密钥发生变化，所有加密字段和所有会话都将永久无法读取。请像对待数据本身一样对待这个密钥：做好备份，并确保所有容器中使用的密钥完全一致。
:::

在安装之前，值得认真了解这一点。@doc(selfHosting.applicationKeyAndEncryption) 详细说明了这个密钥保护的是什么、如何妥善保存它，以及唯一安全的主动轮换方式。

## 你的职责

自托管意味着你就是运维者。具体来说，包括：

- **安装与升级。** 两者都是简短且有文档说明的 Docker 操作流程。
- **备份。** 应用内部没有自动备份功能，你需要自行备份数据库和存储卷，以及应用密钥。
- **邮件发送。** 全新安装的实例只会把邮件记录到日志中而不会真正发送，因此在你配置好邮件服务之前，邀请和登录链接都无法送达任何人。
- **让三个角色保持运行。** 尤其要注意，如果 queue 或 scheduler 容器停止运行，后台任务和每日维护会在你毫无察觉的情况下停止执行。

Alex 为自己的收藏者俱乐部运行着一个实例，在完成初始设置之后，他每个月只需要花几分钟维护它。这不是一项繁重的运维工作，但确实是你自己的责任。

## 本章节内容

建议大致按以下顺序阅读本章节的页面：

1. @doc(selfHosting.installDocker)。从零开始，搭建一个可运行的实例。
2. @doc(selfHosting.configure)。你实际会用到的环境变量。
3. @doc(selfHosting.setupEmailDelivery)。让邀请邮件和魔法链接真正能够发送出去。
4. @doc(selfHosting.applicationKeyAndEncryption)。最重要的一条运维原则。
5. @doc(selfHosting.upgrade)。安全地升级到新版本。
6. @doc(selfHosting.backupAndRestore)。保护你的数据。
7. @doc(selfHosting.scheduledJobs)。应用每晚自动执行的任务。
8. @doc(instanceAdmin.grantAccess)。初始化服务器级管理员权限。
9. @doc(instanceAdmin.panel)。该管理员能看到什么、能做什么。
10. @doc(selfHosting.cliCommands)。运维者需要用到的 artisan 命令。
11. @doc(selfHosting.addLanguage)。界面是如何被翻译的。

## 接下来去哪里

- 准备好安装了吗？前往 @doc(selfHosting.installDocker)。
- 更想要一份带引导的端到端教程？可以跟着 @doc(tutorials.selfHostWithDocker, "自托管教程") 一步步操作。
