---
id: collectionTypes.importExport
title: 导入和导出收藏类型
slug: daoru-he-daochu-shoucang-leixing
section: zhengli-shoucang-mulu
---

# 导入和导出收藏类型

精心搭建的 @doc(collectionTypes.overview, "收藏类型") 值得分享。KolleK 可以把一个类型定义导出为 JSON 文件，也可以再导入回来，方便你在不同账户之间复制设置、分享给其他收藏者，或者在大改之前留一份快照。

你需要编辑者或所有者角色。

## 会移动什么，不会移动什么

导出内容只包含类型定义本身：名称、颜色、字段分组、自定义字段，以及任何单选字段的选项。

:::note
导出类型不会导出藏品或藏品数据。目前还没有藏品或整个收藏的导入导出功能。具体进展见 @doc(troubleshooting.featureStatus, "功能状态页面")，现有的可移植性方式见 @doc(dataSafety.backupCollectionData)。
:::

## 导出一个类型

::::steps
:::step title="打开类型"
在账户设置中打开**收藏类型**，选择你要导出的类型。
:::

:::step title="导出它"
选择**导出**，KolleK 会下载一份描述该类型的 JSON 文件。

::screenshot{label="带导出选项的类型编辑器"}
:::
::::

这个文件是纯文本，你可以阅读它、把它保存到备份中，或者发给别人。

## 导入一个类型

导入操作是通过粘贴 JSON 完成的，所以先在任意文本编辑器中打开收到的文件，复制其中的内容。

::::steps
:::step title="开始导入"
在账户设置中打开**收藏类型**，选择**导入**。
:::

:::step title="粘贴 JSON"
把类型定义粘贴到输入框中并确认。KolleK 会校验内容，并创建出带有对应分组、字段和选项的类型。

::screenshot{label="带有粘贴 JSON 的导入表单"}
:::

:::step title="检查结果"
打开新建的类型，确认字段是否符合预期，然后把它关联到一个收藏就可以开始使用了。
:::
::::

## 一个实际例子

Noah 的朋友也收藏黑胶唱片，已经完善了一个“黑胶唱片”类型，字段分成了两组：发行信息（艺术家、专辑、发行年份）和压制细节（压制版本、转速、彩胶）。Noah 没有手动重新搭建，而是让朋友导出，把 JSON 粘贴进自己的账户，几秒钟内就得到了一模一样的结构。

如果你想知道导入器期望的确切格式，可以先导出任意一个现有类型（比如现成的漫画类型），把它当作模板。你自己导出的内容总能顺利导回来。

## 接下来

- 在 @doc(collectionTypes.setup) 中完善导入的类型。
- 在 @doc(dataSafety.backupCollectionData) 中了解还有哪些内容可以或不可以导出。
