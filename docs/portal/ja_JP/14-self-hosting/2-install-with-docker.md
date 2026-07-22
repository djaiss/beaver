---
id: selfHosting.installDocker
title: Dockerでインストール
slug: docker-de-insutoru
section: serufu-hosutingu
---

# Dockerでインストール

これは公式のインストールガイドです。Dockerが動くマシンから、最初のアカウントが作成された稼働中のKolleKインスタンスまでを案内します。全体でおよそ15分程度かかります。

リポジトリの`docker/README.md`には、運用者の視点から同じ手順が記載されており、コードと同期して保たれています。このページとそのファイルの内容が食い違う場合は、`docker/README.md`を信頼してください。

## 始める前に

必要なもの:

- **Docker Engine 24以降**と**Composeプラグイン**(`docker compose`)が動くマシン。
- クローンまたはダウンロードしたKolleKリポジトリ。
- 環境ファイルに集中する10分間。重要なミスが起きるのはここです。

それ以外は必要ありません。スタックには独自のMySQLデータベースが含まれており、セッション、キャッシュ、キューはすべてデータベースに保存されるため、Redisをインストールする必要はありません。

## インストール

::::steps
:::step title="環境ファイルを作成する"
リポジトリのルートから、Docker用の環境設定テンプレートをコピーします。

```bash
cp .env.docker.example .env
```

このファイルがスタック全体を制御します。次の2つの手順で編集していきます。
:::

:::step title="アプリケーションキーを生成する"
キーを生成し、出力された値をコピーします。

```bash
docker compose run --rm app php artisan key:generate --show
```

表示された値を`.env`の`APP_KEY`として貼り付けます。このキーは、保存されているデータを暗号化します。**今設定し、後から絶対に変更しないでください。** キーを変更すると、暗号化されたすべてのフィールドとすべてのセッションが恒久的に読み取れなくなります。まだ読んでいない場合は、先に進む前に@doc(selfHosting.applicationKeyAndEncryption)を読んでください。
:::

:::step title="パスワードとURLを確認する"
`.env`内の`DB_PASSWORD`と`DB_ROOT_PASSWORD`をプレースホルダーの値から変更し、`APP_URL`にユーザーがアクセスするアドレスを設定します。デフォルトは`http://localhost:8000`で、自分のマシンで最初に試す分にはこれで問題ありません。
:::

:::step title="スタックを起動する"
すべてをビルドして起動します。

```bash
docker compose up -d --build
```

最初のビルドには数分かかります。完了すると、webコンテナが自動的にデータベースマイグレーションを適用し、インスタンスが`APP_URL`で起動します。
:::

:::step title="最初のアカウントを作成する"
ブラウザでそのURLを開き、登録ページからサインアップします。これにより、@doc(accounts.create)で説明されている通り、あなた個人のユーザーと最初のアカウントが作成されます。

::screenshot{label="インストール直後のインスタンスの登録ページ"}
:::

:::step title="自分にインスタンス管理者権限を付与する"
サーバー全体の管理パネルを使いたい場合は、自分のユーザーにフラグを付与します。

```bash
docker compose exec app php artisan beaver:make-instance-administrator you@example.com
```

これによって何ができて何ができないかについては、@doc(instanceAdmin.grantAccess)を参照してください。
:::
::::

## 実際に動いているもの

Composeスタックは4つのコンテナを起動します。そのうち3つは同じKolleKイメージを、`CONTAINER_ROLE`環境変数で選択された異なる役割で実行します。

- **app**は、nginxとPHPを通じてWebアプリケーションを提供します。データベースマイグレーションを実行するのはこのコンテナだけで、起動時に実行します。
- **queue**は、`high`、`default`、`low`の各キューからバックグラウンドジョブ(メール送信、配信、ログ記録)を処理します。
- **scheduler**は、@doc(selfHosting.scheduledJobs)で説明されている日次メンテナンスジョブを実行します。

4つ目のコンテナは**mysql**で、MySQL 8.4を実行します。

データは、コンテナとは独立した2つの名前付きDockerボリュームに保存されます。データベース用の`db-data`と、アップロードされた写真や書類用の`storage-data`です。コンテナは自由に再ビルド・置き換えできますが、ボリュームは維持されます。

:::note
3つのアプリケーションコンテナはすべて同じ`.env`を、とりわけ同じ`APP_KEY`を共有する必要があります。Composeファイルはすでにこれを実現するように構成されています。セットアップをカスタマイズする場合も、この構成は維持してください。
:::

## マイグレーションを自分で実行したい場合

デフォルトでは、webコンテナは起動するたびにデータベースをマイグレーションするため、アップグレード時に手を加える必要がありません。手動で制御したい場合は、`.env`で`RUN_MIGRATIONS=false`を設定し、必要なときに自分でマイグレーションを実行してください。

```bash
docker compose exec app php artisan migrate --force
```

## 次に読むべきもの

- @doc(selfHosting.configure)を読んで、`.env`が他に何を制御しているかを理解しましょう。
- @doc(selfHosting.setupEmailDelivery)でメールを機能させましょう。設定するまでは、招待やサインインリンクは受信箱ではなくログファイルに送られます。
- 実際のデータを投入する前に、@doc(selfHosting.backupAndRestore, "バックアップ")を設定しましょう。
</content>
