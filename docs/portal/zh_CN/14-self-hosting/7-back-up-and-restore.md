---
id: selfHosting.backupAndRestore
title: 备份与恢复你的实例
slug: beifen-yu-huifu
section: zi-tuoguan
---

# 备份与恢复你的实例

KolleK 内部没有自动备份功能。保护数据是运维者的职责，本页就是具体操作流程。就目前而言，这也是“如何导出所有数据”这个问题的真正答案，@doc(dataSafety.backupCollectionData) 从收藏者的角度对此做了说明。

## 完整备份包含什么

三样东西，缺一不可：

1. **数据库**，存放在 `db-data` 数据卷中，包含每一条记录：账户、收藏、藏品、副本、历史记录。
2. **存储卷** `storage-data`，包含每一张上传的照片和每一份文档。
3. **应用密钥**，也就是 `.env` 中的 `APP_KEY`（如果设置了 `APP_PREVIOUS_KEYS`，也要一并包含）。

:::warning
没有对应应用密钥的备份根本算不上备份。没有当初写入数据时使用的密钥，加密字段恢复出来只是一堆无法读取的密文。请将密钥与你每一次的备份一起保存，或至少同步保存。参见 @doc(selfHosting.applicationKeyAndEncryption)。
:::

## 备份

导出数据库：

```bash
docker compose exec mysql mysqldump -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" > kollek-backup.sql
```

打包存储卷：

```bash
docker run --rm -v kollek_storage-data:/data -v "$PWD":/backup alpine tar czf /backup/kollek-storage.tar.gz -C /data .
```

把这两个文件，连同一份 `.env`，复制到服务器之外的地方保存。建议用每晚运行的 cron 任务自动完成，并保留多个历史版本；一份从未验证过能否恢复的备份，只是一种侥幸，算不上真正的方案。

## 恢复

在一台全新的机器上，按以下顺序恢复：

1. 按照 @doc(selfHosting.installDocker) 安装相同版本的 KolleK，但要使用备份中的 `APP_KEY`（以及 `APP_PREVIOUS_KEYS`），而不是重新生成一个新密钥。
2. 先启动一次服务栈，让数据卷被创建出来，然后导入数据库备份：

```bash
docker compose exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" < kollek-backup.sql
```

3. 将存储卷的备份文件解压到存储卷中：

```bash
docker run --rm -v kollek_storage-data:/data -v "$PWD":/backup alpine tar xzf /backup/kollek-storage.tar.gz -C /data
```

4. 使用 `docker compose up -d` 重启服务栈，然后登录验证是否恢复成功。

## 会删除所有数据的命令

:::warning
`docker compose down -v` 会删除具名数据卷，也就是数据库和所有上传的文件。切勿在生产实例上使用 `-v` 参数。不带该参数的 `docker compose down` 是安全的，不会影响数据卷。
:::

## 接下来去哪里

- 在 @doc(selfHosting.applicationKeyAndEncryption) 中了解这个密钥保护的是什么。
- 在 @doc(dataSafety.backupCollectionData) 中查看收藏者可以在应用内导出哪些内容。
