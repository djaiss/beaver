---
id: dataSafety.backupCollectionData
title: 备份你的收藏数据
slug: beifen-nide-shoucang-shuju
section: shuju-anquan-yu-richang-weihu
---

# 备份你的收藏数据

"我该如何把数据导出来"这个问题值得一个直截了当的答案。本页会如实说明目前 KolleK 在应用内能导出什么、还不能导出什么，以及在此之前真正可靠的备份方式是什么。

## 目前可以导出的内容

**收藏类型定义。** @doc(collectionTypes.overview, "收藏类型")可以导出为一个 JSON 文件（包含名称、颜色、字段组、字段和选项），并导入到任意 KolleK 账户中。参见 @doc(collectionTypes.importExport)。

这就是目前完整且如实的清单。

## 目前还不能导出的内容

目前还没有内置的功能可以导出藏品、副本、照片，或整个收藏，也没有相应的导入功能。你还无法通过界面把目录数据以文件形式从应用中导出。

:::note
藏品和收藏的导入导出功能已经列入了功能规划。@doc(troubleshooting.featureStatus, "功能状态页面")会持续更新这方面的进展，请以那里的信息为准，不要凭猜测判断。
:::

如果你现在就需要以结构化方式访问你的数据，@doc(api.overview, "JSON API")可以读取你账户中的所有内容，对于有技术背景的用户来说，这是一条可行的路径。

## 目前真正可靠的备份方式

如果你的实例是自托管的，可靠的备份应该在实例层面进行：一份数据库转储，加上存放照片和文档的存储卷的归档。这样能捕获所有内容，包括应用内导出功能无法覆盖的部分。具体操作步骤见 @doc(selfHosting.backupAndRestore)。

如果是别人为你托管 KolleK，那么备份能力掌握在对方手中。请询问他们的备份方案，这是一个合理且重要的问题。

## 接下来去哪里

- 自托管？请在 @doc(selfHosting.backupAndRestore) 中设置真正的备份。
- 在账户之间迁移类型设置的方法，请参见 @doc(collectionTypes.importExport)。
- 查看更多规划内容，请前往@doc(troubleshooting.featureStatus, "功能状态页面")。
