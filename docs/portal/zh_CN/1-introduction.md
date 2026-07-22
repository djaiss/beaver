---
id: portal.index
title: KolleK 文档
slug: wendang
section: portal
---

# KolleK 文档

欢迎。KolleK 是一个私密的、可自托管的收藏管理平台，可以用来管理你收藏的任何东西：漫画、黑胶唱片、硬币、手表、葡萄酒、书籍、卡牌、电子游戏，或者其他任何你拥有的物品。你可以创建收藏，用自定义字段描述它们，为藏品添加照片和标签，并追踪你持有的每一件实体副本，包括它的品相、存放位置、价值和完整历史记录。

本文档站按照循序渐进的顺序组织，新读者可以从头开始阅读，不会在准备好之前遇到进阶概念。

## 章节导览

1. @doc(gettingStarted.index)。KolleK 是什么、如何创建你的账户，以及你编目的第一件藏品。
2. @doc(coreConcepts.index)。核心概念模型：账户、收藏、藏品、副本，以及与之相关的一切。
3. @doc(coreFeatures.index)。日常编目工作：收藏、藏品、副本、照片和标签。
4. @doc(copyHistory.index)。金钱、价值、保险、外借、保养、来源、转移和文档记录。
5. @doc(organizing.index)。类型与自定义字段、分类、套装、系列、位置、标签，以及照片库。
6. @doc(insights.index)。统计页面与仪表盘。
7. @doc(collaboration.index)。邀请他人、角色设置，以及共享收藏。
8. @doc(accountAndProfile.index)。个人信息、语言、头像和账户设置。
9. @doc(security.index, "安全")。两步验证、恢复代码、魔法链接、密码和 API 密钥。
10. @doc(dataSafety.index)。回收站、删除操作，以及导出你的数据。
11. @doc(tutorials.index)。将各项功能串联起来的完整端到端教程。
12. @doc(developers.index)。面向基于 KolleK 进行开发的用户的身份验证、约定和 Webhook。
13. @doc(selfHosting.index, "自托管与管理")。实例的安装、配置、升级、备份和管理。
14. @doc(troubleshooting.index)。当某些行为不符合预期时的快速解答。
15. @doc(reference.index)。术语表、所有字段与状态选项，以及 KolleK 发送的每一封邮件。

## 你应该从哪里开始

**如果你是收藏者。** 阅读 @doc(kollek.whatIs)、@doc(accounts.create, "创建你的账户")，跟着 @doc(gettingStarted.checklist, "新手入门清单") 操作，然后学习 @doc(kollek.howOrganized, "KolleK 的组织方式") 以及 @doc(items.itemsVsCopies, "藏品与副本的区别") 这一关键概念。在此基础上，@doc(coreFeatures.index) 中的指南会带你完成日常编目工作，而当你想要记录价值、外借和来源信息时，@doc(copyHistory.index) 随时可用。

**如果你的账户有其他人在使用。** 除了收藏者需要阅读的全部内容之外，还应阅读 @doc(accounts.usersAndRoles, "账户、用户与角色")、@doc(collaboration.index) 章节、@doc(accounts.settings, "账户设置")，以及 @doc(dataSafety.index, "数据安全")。

**如果你运行着一个实例。** 先阅读 @doc(kollek.hostingOptions)，然后从 @doc(selfHosting.index, "概述") 开始，依次通读整个 @doc(selfHosting.index, "自托管与管理") 章节，并特别留意 @doc(selfHosting.applicationKeyAndEncryption, "应用密钥") 部分。

**如果你正在基于 API 进行开发。** 阅读 @doc(api.overview) 和 @doc(api.authenticate, "通过 API 进行身份验证")，然后参考 `/docs/api` 上自动生成的接口文档。你也可以阅读 @doc(webhooks.overview, "Webhook")，不过目前还没有任何事件会触发它们。
