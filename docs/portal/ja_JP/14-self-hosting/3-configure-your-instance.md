---
id: selfHosting.configure
title: インスタンスを設定する
slug: insutansu-no-settei
section: serufu-hosutingu
---

# インスタンスを設定する

インスタンスに関するすべての設定は、@doc(selfHosting.installDocker, "インストール")の際に作成した`.env`ファイルを通じて行います。このページでは、テンプレートに含まれるすべての変数を羅列するのではなく、運用者が実際に触れる設定を、その役割ごとにまとめて説明します。

`.env`を変更したら、コンテナを再作成して反映させます。

```bash
docker compose up -d
```

## 名前とURL

- `APP_NAME`は、インターフェースやメールに表示される名前です。デフォルトは`Kollek`です。
- `APP_URL`は、インスタンスの公開アドレスです。メール内のリンクはこの値から生成されるため、ユーザーが実際に使うアドレスに設定する必要があります。
- `APP_PORT`は、webコンテナが公開するホスト側のポートで、デフォルトは`8000`です。

## アプリケーションキー

`APP_KEY`は、保存されている機密データを暗号化します。インストール時に一度だけ設定し、軽い気持ちで変更してはいけません。これは@doc(selfHosting.applicationKeyAndEncryption, "専用のページ")を設けるほど重要な項目で、そのページでは`APP_PREVIOUS_KEYS`によるローテーションの仕組みについても説明しています。

## データベース

`DB_DATABASE`、`DB_USERNAME`、`DB_PASSWORD`、`DB_ROOT_PASSWORD`は、同梱のMySQLコンテナを設定します。初回起動前に、両方のパスワードをプレースホルダーの値から変更してください。`RUN_MIGRATIONS`は、webコンテナが起動時にマイグレーションを実行するかどうかを制御します(デフォルトは`true`)。

## メール

`MAIL_MAILER`は、インスタンスからメールをどう送信するかを決めるもので、デフォルトは`log`です。

:::note
デフォルトの`log`メーラーでは、メールは一切送信されません。招待、マジックリンク、パスワードリセット、セキュリティ通知は、代わりにアプリケーションログに書き込まれます。実際のメーラーを設定することは、ほぼすべてのインスタンスで必要になる、唯一と言ってよい設定作業です。@doc(selfHosting.setupEmailDelivery)を参照してください。
:::

## ファイルストレージ

`FILESYSTEM_DISK`のデフォルトは`local`で、アップロードされた写真や書類は`storage-data`ボリュームに保存されます。代わりにS3互換のオブジェクトストレージを使うには、`s3`に設定し、`AWS_ACCESS_KEY_ID`、`AWS_SECRET_ACCESS_KEY`、`AWS_DEFAULT_REGION`、`AWS_BUCKET`、そしてAWS以外のプロバイダーの場合は`AWS_ENDPOINT`の各変数を設定します。どちらの場合も、ファイルは公開URLとしてではなく、アカウントによる確認を伴う非公開のルートを通じてユーザーに提供されます。

## 日常のメンテナンス設定

- `TRASH_RETENTION_DAYS`は、ソフトデリートされたオブジェクトが@doc(dataSafety.restoreFromTrash, "ゴミ箱")に残り、夜間の完全削除処理で消されるまでの日数です。デフォルトは30日です。
- `ACCOUNT_DELETION_NOTIFICATION_EMAIL`は、ユーザーが自分自身のユーザーを削除したとき、または@doc(users.inactiveDeletion, "非アクティブユーザーの整理")によって削除されたときに通知するアドレスです。離脱に気づけるよう、自分自身のアドレスを設定しておきましょう。

## 公開マーケティングサイト

`SHOW_MARKETING_SITE`のデフォルトは`false`で、インスタンスはアプリケーション本体のみを提供します。`true`に設定すると、公開マーケティングページと、`/docs/api`で生成されるAPIリファレンスも提供されます。ほとんどの非公開インスタンスではオフのままにしますが、開発者がAPIリファレンスをローカルで見られるようにしたい場合はオンにしてください。

## 設定不要なもの

セッション(`SESSION_DRIVER`)、キャッシュ(`CACHE_STORE`)、キュー(`QUEUE_CONNECTION`)は、すべて標準で`database`に保存されます。提供されているスタックにはこのデフォルトで問題なく、Redisなどの追加サービスを加える必要もありません。変更する理由を正確に把握していない限り、これらはそのままにしておいてください。

## 次に読むべきもの

- @doc(selfHosting.setupEmailDelivery)で、実際にメールが届くようにしましょう。
- @doc(selfHosting.applicationKeyAndEncryption)で、保護すべきキーについて理解しましょう。
- @doc(selfHosting.backupAndRestore, "バックアップ")を用意しましょう。
</content>
