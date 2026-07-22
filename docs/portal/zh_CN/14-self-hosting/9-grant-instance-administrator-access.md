---
id: instanceAdmin.grantAccess
title: 授予实例管理员权限
slug: shouyu-shili-guanliyuan-quanxian
section: zi-tuoguan
---

# 授予实例管理员权限

实例管理员是负责照看服务器本身的人，拥有一个可以查看实例上所有账户的管理面板。本页说明这个标志是什么、如何授予，以及围绕它的各种保护机制。

## 这个标志是什么，又不是什么

实例管理员标志作用于整个服务器，与 @doc(accounts.usersAndRoles, "账户角色") 完全无关。它只授予一件事：访问 @doc(instanceAdmin.panel, "实例管理面板") 的权限。

- 它不会在管理员自己的账户内赋予任何额外权限。如果一位实例管理员在自己的账户中是查看者角色，他依然无法编辑该账户中的藏品。
- 这个标志是针对用户的，而不是针对账户的。应该授予实际操作服务器的那个人，通常就是你自己。

Alex 负责运维俱乐部的实例，他的用户带有这个标志，同时在自己的账户中只是一个普通的所有者。这两件事互不相关。

## 授予与撤销

这个标志只能通过命令行管理，这是刻意的设计：获取这个服务器级面板的初始访问权限，本身就应该要求你先拥有服务器的访问权限。

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

撤销方式相同：

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com --revoke
```

已有的管理员也可以在面板内切换其他用户的该标志。

## 为什么这个面板假装自己不存在

对没有这个标志的人来说，访问 `/instance-admin` 得到的回应是 **404 Not Found**，而不是“访问被拒绝”。面板不会向无法使用它的人透露自己的存在，因此探测一个实例也无法得到任何线索。如果你已经为自己授予了这个标志，却仍然看到 404，请检查你登录的用户是否正是被授予标志的那个用户。

## 防止被锁在外面的保护机制

两条规则保护实例不会失去管理员：

- 管理员无法在面板中撤销自己的标志。
- 管理员无法在面板中删除自己的用户。

因此，这个面板永远不可能被用来把所有人都锁在面板之外。而且即使所有管理员都不存在了，前面提到的命令行方式依然始终有效，因为它只需要服务器的访问权限。

## 接下来去哪里

- 在 @doc(instanceAdmin.panel) 中了解这个面板能做什么。
- 在 @doc(selfHosting.cliCommands) 中浏览其他运维命令。
