---
id: selfHosting.cliCommands
title: コマンドラインで管理する
slug: komando-rain-de-kanri
section: serufu-hosutingu
---

# コマンドラインで管理する

一部の運用者向けタスクは、Webアプリではなくコマンドラインで行います。このページでは、インスタンスを運用する際に実際に必要になりそうなartisanコマンドを、それぞれ詳しく説明しているページへのリンクとともに一覧にしています。

Dockerでのインストールでは、すべてのコマンドをwebコンテナ経由で実行します。

```
docker compose exec app php artisan <command>
```

## 日常の運用

### インスタンス管理者権限の付与と剥奪

```
php artisan kollek:make-instance-administrator you@example.com
php artisan kollek:make-instance-administrator you@example.com --revoke
```

指定したメールアドレスのユーザーに、サーバー全体の管理者フラグを付与(または剥奪)します。インストール後、最初の管理者を用意する方法はこれです。@doc(instanceAdmin.grantAccess)を参照してください。

### Webhookエンドポイントを作成する

```
php artisan kollek:create-webhook-endpoint you@example.com https://example.com/hooks --label="My receiver"
```

ユーザーのためにWebhookエンドポイントを登録し、そのIDと署名用シークレットを出力します。ユーザー自身がプロフィール設定から行うこともできます。なお、現時点ではWebhookを発火させるアプリケーションイベントはまだありません。@doc(webhooks.overview)を参照してください。

### 写真検索インデックスを再構築する

```
php artisan photos:rebuild-search-index
```

写真ライブラリを支える検索インデックスを再構築し、不足している画像の寸法をさかのぼって補完します。写真画面が導入されたバージョンにアップグレードした後、一度だけ実行してください。何度実行しても安全で、ファイルが見つからない写真はスキップされ、それ以外には何も変更を加えません。@doc(selfHosting.upgrade)を参照してください。

### 翻訳用にロケールをひな形化する

```
php artisan kollek:localize fr_FR
```

アプリケーション内の翻訳可能な文字列をすべて抽出し、そのロケールの`lang/`配下のJSONファイルに同期します。@doc(selfHosting.addLanguage)を参照してください。

## 開発用のみ

コードベースにはあと2つコマンドがありますが、どちらも本番インスタンスで使うものではありません。`kollek:bruno`は、APIクライアントのテスト用にシードデータでデータベースをリセットするもので、実データを破壊してしまいます。`kollek:sync-skills`は、プロジェクト自身のツール群を保守するためのものです。運用者としては、どちらも気にする必要はありません。

:::warning
本番インスタンスで`kollek:bruno`を絶対に実行しないでください。データベースを消去し、デモデータで再シードします。
:::

## 次に読むべきもの

- @doc(instanceAdmin.grantAccess)で、最初の管理者を用意しましょう。
- @doc(selfHosting.upgrade)で、インスタンスを最新の状態に保ちましょう。
- @doc(selfHosting.addLanguage)で、インターフェースを翻訳しましょう。
</content>
