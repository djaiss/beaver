---
id: selfHosting.setupEmailDelivery
title: メール配信を設定する
slug: meru-haishin-no-settei
section: serufu-hosutingu
---

# メール配信を設定する

メールは、KolleKがブラウザセッションの外にいる人に届く手段です。@doc(collaboration.invitePeople, "招待")、@doc(auth.magicLinks, "マジックリンク")、パスワードリセット、メール確認、@doc(security.alertEmails, "セキュリティ通知")は、すべてメールで届きます。配信を設定するまで、これらはどこにも届きません。

## デフォルトでは何も送信されない

新規インスタンスは`MAIL_MAILER=log`の状態で出荷されます。すべてのメールは、送信される代わりにアプリケーションログファイルに書き込まれます。これは意図的な仕様です。設定が半端なインスタンスが、誤ったアドレスから気づかないうちにメールを送ってしまうことを防ぎ、テスト中には何が送信されるはずだったかを正確に確認できます。

:::note
新しいインスタンスで「招待メールが届かない」と言われた場合、ほとんどの原因はこのデフォルト設定です。メール自体はログファイルの中に存在します。@doc(troubleshooting.emailDelivery)を参照してください。
:::

実際にメールを送信する方法は2つサポートされています。任意のSMTPサーバーか、Resendサービスです。

## 方法1: SMTP

::::steps
:::step title="メーラーとサーバーの詳細を設定する"
`.env`で次のように設定します。

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

SMTP認証情報を持つトランザクションメールプロバイダーや、自分で運用しているメールサーバーであれば、どれでも使えます。
:::

:::step title="送信者情報を設定する"
ユーザーに表示されるアドレスと名前を設定します。

```bash
MAIL_FROM_ADDRESS=kollek@yourdomain.com
MAIL_FROM_NAME="KolleK"
```

自分で管理していて、送信用に設定済み(プロバイダー側でSPFとDKIMを設定済み)のドメインを使ってください。そうしないと、メールが迷惑メールとして扱われます。
:::

:::step title="反映してテストする"
コンテナを再作成し、たとえばサインインページからマジックリンクをリクエストするなどして、実際にメールを送信させてみます。

```bash
docker compose up -d
```
:::
::::

## 方法2: Resend

[Resend](https://resend.com)を使う場合は、次のように設定します。

```bash
USE_RESEND=true
RESEND_API_KEY=re_your_api_key
```

これにより、メールはSMTPではなくResendのAPIを通じて送信され、送信のたびにResendのメッセージIDも一緒に記録されます。

## 配信が機能しているか確認する

KolleKは、送信したすべてのメールをユーザーごとに、件名、本文、配信ステータスとともに記録します。テスト送信の後は、次の2箇所を確認してください。

- 自分の受信箱。これは当然です。
- 受信者のプロフィール内にある**送信済みメール**ページ。インスタンスがそのユーザーに送ったメールが一覧表示されます。@doc(activity.logAndSentEmails, "自分の操作履歴と送信済みメール")を参照してください。

よくある失敗の兆候:

- **何も届かず、エラーも出ない。** メーラーがまだ`log`のままです。`.env`の変更がコンテナの再作成によって反映されているか確認してください。
- **メールは送信されるが迷惑メールに入る。** 送信元ドメインが認証されていません。プロバイダー側でSPFとDKIMを設定してください。
- **ログに送信エラーが出る。** 認証情報またはホストの詳細が間違っています。queueワーカーのログに、プロバイダー側のエラーメッセージが記録されています。

メールはバックグラウンドのキューによって送信されるため、**queue**コンテナが稼働していなければ、インスタンスから何も送信されません。

## 次に読むべきもの

- @doc(reference.emailsSent)で、インスタンスが送信するメールの種類を把握しましょう。
- @doc(troubleshooting.emailDelivery)で、配信の問題を診断しましょう。
</content>
