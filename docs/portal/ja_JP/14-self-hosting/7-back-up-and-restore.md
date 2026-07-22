---
id: selfHosting.backupAndRestore
title: インスタンスをバックアップ・復元する
slug: bakkuappu-to-fukugen
section: serufu-hosutingu
---

# インスタンスをバックアップ・復元する

KolleK内部には、自動バックアップの仕組みはありません。データを守るのは運用者の仕事であり、このページがその手順です。また今のところ、これは「すべてをエクスポートするにはどうすればよいか」という問いへの、@doc(dataSafety.backupCollectionData)がコレクター側の視点で説明している内容とはまた別の、実質的な答えでもあります。

## 完全なバックアップとは何か

3つの要素があり、そのすべてが重要です。

1. **データベース**(`db-data`ボリューム)。アカウント、コレクション、アイテム、コピー、履歴を含むすべてのレコード。
2. **ストレージボリューム**(`storage-data`)。アップロードされたすべての写真と書類。
3. **アプリケーションキー**。`.env`内の`APP_KEY`(設定されていれば`APP_PREVIOUS_KEYS`も)。

:::warning
対応するアプリケーションキーのないバックアップは、バックアップとは言えません。暗号化されたフィールドは、書き込んだときのキーがなければ、読み取れない暗号文として復元されます。バックアップを取るたびに、そのキーも一緒に、あるいはそばに保管してください。@doc(selfHosting.applicationKeyAndEncryption)を参照してください。
:::

## バックアップを取る

データベースをダンプします。

```bash
docker compose exec mysql mysqldump -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" > kollek-backup.sql
```

ストレージボリュームをアーカイブします。

```bash
docker run --rm -v beaver_storage-data:/data -v "$PWD":/backup alpine tar czf /backup/kollek-storage.tar.gz -C /data .
```

両方のファイルと、`.env`のコピーを、サーバーの外のどこかにコピーしてください。夜間のcronジョブで自動化し、複数世代を保持しましょう。一度も復元したことのないバックアップは、希望的観測であって計画とは言えません。

## 復元する

新しいマシンでは、次の順序で復元します。

1. @doc(selfHosting.installDocker)に従って同じKolleKのバージョンをインストールしますが、新しいキーを生成する代わりに、バックアップから`APP_KEY`(と`APP_PREVIOUS_KEYS`)を設定します。
2. ボリュームが作成されるように一度スタックを起動し、その後データベースのダンプを読み込みます。

```bash
docker compose exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" < kollek-backup.sql
```

3. ストレージのアーカイブをストレージボリュームに展開します。

```bash
docker run --rm -v beaver_storage-data:/data -v "$PWD":/backup alpine tar xzf /backup/kollek-storage.tar.gz -C /data
```

4. `docker compose up -d`でスタックを再起動し、サインインして確認します。

## すべてを削除してしまうコマンド

:::warning
`docker compose down -v`は、名前付きボリュームを削除します。つまりデータベースとアップロードされたすべてのファイルが消えます。本番のインスタンスでは`-v`フラグを絶対に使わないでください。単に`docker compose down`とするだけであれば安全で、ボリュームはそのまま残ります。
:::

## 次に読むべきもの

- @doc(selfHosting.applicationKeyAndEncryption)で、このキーが何を保護しているかを理解しましょう。
- @doc(dataSafety.backupCollectionData)で、コレクターがアプリ内から何をエクスポートできるかを確認しましょう。
</content>
