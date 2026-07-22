---
id: reference.glossary
title: 术语表
slug: shuyu-biao
section: cankao-shouce
---

# 术语表

所有产品术语汇总于此。每个词条都链接到完整解释该概念的页面。术语按照你在产品中接触它们的顺序排列，从工作空间到单个副本的记录。

## 工作空间

**账户（Account）。** 你的私人工作空间，是你创建的一切内容的边界。每个收藏、藏品和设置都只属于一个账户。参见 @doc(accounts.usersAndRoles)。

**用户（User）。** 一个可以登录的人。一个用户只属于一个账户，无法用同一个邮箱加入第二个账户。参见 @doc(accounts.usersAndRoles)。

**角色（Role）。** 决定用户在其账户中能做什么：查看者只能查看，编辑者可以编目，所有者还可以管理账户。参见 @doc(collaboration.rolesInPractice, "在实践中理解三种角色")。

## 目录

**收藏（Collection）。** 你命名的顶层分组，例如"我的漫画"或"酒窖"。收藏容纳藏品，并有自己的货币单位和可见性设置。参见 @doc(collections.overview)。

**收藏类型（Collection type）。** 你收藏的一类事物（漫画、黑胶唱片、葡萄酒），决定其藏品要记录哪些自定义字段。类型在整个账户内共享。参见 @doc(collectionTypes.overview)。

**自定义字段（Custom field）。** 你在收藏类型上定义的一项细节，例如"期号"或"年份"。其值记录在每个藏品上。参见 @doc(collectionTypes.overview)。

**字段组（Field group）。** 一个具名分区，例如"出版信息"，用来让藏品表单上一长串自定义字段保持清晰易读。参见 @doc(collectionTypes.setup)。

**藏品（Item）。** 你编目的那类事物，例如"超凡蜘蛛侠 #1"。描述性细节记录在藏品上；你实际拥有的实物则是它的副本。参见 @doc(items.itemsVsCopies)。

**副本（Copy）。** 你实际持有的一个藏品的实物。每个副本都有各自的品相、位置、价值和历史记录。参见 @doc(items.itemsVsCopies)。

## 分组与查找

**分类（Category）。** 一个收藏内部的归档工具。分类可以嵌套，例如漫画分类下嵌套漫威分类。参见 @doc(organizing.categoriesSetsAndSeries)。

**套装（Set）。** 你在一个收藏内尝试集齐的一份有限清单，会与目标数量进行对比追踪。参见 @doc(organizing.categoriesSetsAndSeries)。

**系列（Series）。** 一个可以跨越多个收藏的作品系列，例如跨越书籍和电影的《哈利·波特》。系列不追踪完成度。参见 @doc(organizing.categoriesSetsAndSeries)。

**标签（Tag）。** 一个在账户内所有收藏间共享的自由标签，例如"已签名"。一个藏品可以带有多个标签。参见 @doc(tags.overview)。

**位置（Location）。** 副本实际存放的地方。位置可以嵌套，用来还原真实空间，例如房间里架子上的一个箱子。参见 @doc(locations.overview)。

**品相（Condition）。** 描述副本状态的等级，例如全新或已损坏。参见 @doc(conditions.overview)。

## 副本的历史记录

**交易（Transaction）。** 副本上的一次金钱或所有权事件，例如购买或出售。所有金额都记录在交易中。参见 @doc(copies.recordPaymentsAndValue)。

**估值（Valuation）。** 副本在某个时间点的价值。副本当前的估计价值就是它最近一次的估值。参见 @doc(copies.recordPaymentsAndValue)。

**保险记录（Insurance record）。** 为副本记录的保险信息：保险公司、投保价值、保单详情和状态。参见 @doc(copies.insure)。

**借还记录（Loan）。** 记录你借出或借入的副本的保管情况，包括日期、对方以及归还细节。参见 @doc(loans.lendAndBorrow)。

**保养记录（Maintenance record）。** 对副本进行的保养或修复工作，例如清洁或修复。参见 @doc(copies.recordMaintenance)。

**来源事件（Provenance event）。** 副本所有权与真伪故事中的一个章节，例如取得、展出或鉴定。参见 @doc(copies.traceProvenance)。

**位置历史（Location history）。** 副本随时间存放地点变化的带日期记录。移动副本会结束上一条记录并开启下一条。参见 @doc(copies.move)。

**文档（Document）。** 与副本或其某项记录一并保存的文件或外部链接，例如某笔交易的收据。参见 @doc(copies.attachDocuments)。

## 访问与安全

**可见性（Visibility）。** 一项收藏设置，记录该收藏面向谁：私密（仅你自己）、共享（账户内所有人），或公开（拥有链接的任何人均可查看，只读）。目前只是记录设置，等分享功能上线后才会强制生效。参见 @doc(sharing.overview)。

**回收站（Trash）。** 已删除的收藏、藏品、副本、分类和套装在被彻底清除前等待的地方，也可以从这里恢复它们。参见 @doc(dataSafety.restoreFromTrash)。

**实例管理员（Instance administrator）。** 一个与账户角色无关的服务器级标志，为运行该实例的人解锁管理面板。参见 @doc(instanceAdmin.grantAccess)。

**API 密钥（API key）。** 一个个人令牌，让脚本或应用能够以你的身份调用 KolleK API。参见 @doc(apiKeys.manage)。

**Webhook。** 你注册的一个 URL，用来接收 KolleK 发出的带签名通知。目前还没有任何应用事件会触发它。参见 @doc(webhooks.overview)。

## 接下来去哪里

- 这些术语能取的每一种选项：@doc(reference.fieldAndStatus)。
- 术语背后的概念详解：@doc(coreConcepts.index)。
