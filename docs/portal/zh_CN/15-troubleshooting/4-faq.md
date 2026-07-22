---
id: troubleshooting.faq
title: 常见问题
slug: changjian-wenti
section: guzhang-paicha
---

# 常见问题

针对反复出现的问题给出简短答案，每一条都链接到系统讲解该主题的页面。

## 藏品和副本有什么区别？

藏品是一类东西，比如"神奇蜘蛛侠 #1"。副本是你实际拥有的一个物理实例。拥有三本同样的漫画，那就是一个藏品、三个副本，每个副本各自有自己的品相、位置、价值和历史记录。这是 KolleK 中最重要的一个概念。参见@doc(items.itemsVsCopies)。

## 我能同时属于多个账户吗？

不能。一个用户只属于一个账户，一个邮箱地址也只能对应一个用户。这也意味着，一个已经拥有自己账户的邮箱，无法接受加入别人账户的邀请。参见@doc(accounts.usersAndRoles)。

## KolleK 真的是免费的吗？

是的。应用内完全没有任何计费功能：没有套餐、没有分级、没有需要付费解锁的功能。自行托管是免费的，不管你怎么运行它，所有功能都是完整包含的。参见@doc(kollek.hostingOptions)。

## 我要怎么导出我的数据？

目前，在应用内部你可以把@doc(collectionTypes.importExport, "收藏类型定义导出为 JSON")。目前还没有单个藏品或整个收藏的导出功能。对于自行托管者来说，完整的答案是对数据库和已上传文件进行实例级别的备份，具体做法见@doc(selfHosting.backupAndRestore)。一份如实的总结在@doc(dataSafety.backupCollectionData)中。

## 为什么我不能移除或降级最后一位所有者？

一个账户必须始终保留至少一位所有者，否则就没有人能管理这个账户、邀请成员，或删除账户了。请先把其他人提升为所有者。参见@doc(collaboration.manageMembersAndRoles)。

## 搜索功能在哪里？

从仪表盘搜索所有内容的功能目前还不可用，你在那里看到的搜索框只是一个占位符。目前可用的功能有：在你打开的收藏内部进行筛选，以及搜索你的照片库。参见@doc(troubleshooting.featureStatus)。

## Webhook 现在能用吗？

只做好了一半。你可以注册端点，每个端点都会获得一个签名密钥，但目前还没有任何应用内事件会触发 webhook。投递机制已经就绪，只是还在等事件被接入。参见@doc(webhooks.overview)。

## 我的数据加密了吗？这能保护什么？

敏感字段使用你实例的密钥在数据库中进行静态加密。如果数据库本身被单独窃取，这能保护其中的内容。但这不是端到端加密：运营这个实例的人掌握着密钥，可以访问这些数据。参见@doc(dataSafety.howProtected)。

## 我能添加自己的品相吗？

可以。在账户设置中打开**藏品品相**，即可添加、重命名或删除品相，包括系统预置的那些（全新、近全新、二手、有磨损、损坏）。参见@doc(conditions.manage)。

## 有东西被删除了，我能找回来吗？

如果被删除的是收藏、藏品、副本、分类，或套装，它会被放进回收站，默认在 30 天内可以恢复。照片、文档和历史记录会被立即删除，无法在应用内恢复。参见@doc(dataSafety.restoreFromTrash)。

## 还是没解决问题？

- 登录问题，参见@doc(troubleshooting.signIn)。
- 邮件收不到，参见@doc(troubleshooting.emailDelivery)。
- 哪些功能已完成、哪些还没有，参见@doc(troubleshooting.featureStatus)。
