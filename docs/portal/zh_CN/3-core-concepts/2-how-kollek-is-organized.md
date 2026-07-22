---
id: kollek.howOrganized
title: KolleK 的组织结构
slug: kollek-de-zuzhi-jiegou
section: hexin-gainian
---

# KolleK 的组织结构

在深入细节之前，这一页先给你展示整体地图。本节其余的每一页都是在放大其中的某一部分。

## 主干：四个层级

你在 KolleK 中编目的一切都遵循一个简单的嵌套结构：

- **@doc(accounts.usersAndRoles, "账户")**是你的工作空间，下面的一切都只属于一个账户。
  - **@doc(collections.overview, "收藏")**是一组命名的物品集合，比如"我的漫画"或"酒窖"。
    - **@doc(items.itemsVsCopies, "藏品")**是一类东西，比如"神奇蜘蛛侠 #1"。
      - **@doc(items.itemsVsCopies, "副本")**是你实际拥有的这个藏品的一个物理实例。

Emma 的账户里有一个"我的漫画"收藏，里面有一个藏品叫"神奇蜘蛛侠 #1"。她拥有两本，所以这个藏品有两个副本，每个副本都有各自的品相、存放位置和价值。

藏品和副本的划分是这套模型的核心，它有@doc(items.itemsVsCopies, "专门的一页")来讲解。如果你只打算读一页概念说明，就读那一页。

## 共用的辅助工具

围绕这条主干，还有几个账户级别通用的工具，它们只需要定义一次，就能在各处复用：

- **@doc(collectionTypes.overview)**决定每一类藏品要记录哪些细节。漫画类型要求填写期号，葡萄酒类型要求填写年份。
- **@doc(organizing.categoriesSetsAndSeries)**以三种不同的方式对藏品进行分组：在一个收藏内归档、追踪一份有限清单直到集齐、以及跨收藏把一个系列串联起来。
- **@doc(tags.overview)**是整个账户共享的自由格式标签，比如"已签名"。
- **@doc(locations.overview)**描述副本的实际存放位置，并且可以嵌套：房间里的架子上的一个盒子。
- **@doc(conditions.overview)**评定副本的状态等级，从全新到损坏。

## 历史记录层

每个副本还携带着@doc(copyHistory.concept, "属于它自己的历史")：你付了多少钱、它随时间的价值变化、保险、借还、保养、来源，以及它存放过的每一个地方。副本本身展示的是它当前的状态，而历史记录讲述的是背后的故事。

## 记清楚这条界限

:::note
描述性和分类性的信息保存在藏品上，而所有和品相、位置、金钱、历史相关的信息都保存在副本上。拿不准的时候，问自己一句："这一点对这类东西的所有副本都成立，还是只对这一个成立？"
:::

## 接下来可以做什么

- 在@doc(accounts.usersAndRoles)中认识工作空间以及其中的成员。
- 直接了解核心概念，参见@doc(items.itemsVsCopies)。
- 更想直接动手而不是先读文档？试试@doc(gettingStarted.quickStart, "五分钟快速上手")。
