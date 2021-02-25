 cafeshop_portfolio
====================
会員制カフェショッピングサイト(仮)のポートフォリオです。

## Description
実際の動作では支払うという概念は存在せず、カートに入れた商品を確認後、
確定を行うと注文履歴が表示、在庫数が更新されます。
更新された在庫数によって商品ページが切り替わる仕様となっております。

初めは会員登録を行い、ログイン後サイトのホームページが閲覧できます。
マイページには登録時の内容と購入履歴が確認できます。
カート内には現在入っている商品/数量/小計/全商品数/合計金額が確認できます。

お問い合わせ画面のメール先は下記ソースコードのコメントアウト部分に
自身のメールアドレスとパスワードを設定する必要があります。

/cafeshop/contact/sendmain.php

DB設計に使用したSQLは下記に記載しています。

/cafeshop/資料/cafeshop.sql

特に意識した点は、見た目より機能を注視しながらも、見た目や機能を
なるべくシンプルに見やすくすることです。
今見ているサイトが『カフェショップ』なのだと無意識に感じてもらうため、
なるべく自然な色合いで統一感を持った機能性を心がけました。


主な機能は以下にまとめています。
1. 会員登録
2. 会員情報更新
3. 退会
4. ログイン/ログアウト
5. パスワード忘れユーザー向け再設定機能
6. 検索機能（商品名）
7. カテゴリー別/商品表示時のページング機能
8. ホームページの画像スライダー (slick使用)
9. お問い合わせメール(PHPMailer使用)
10. クチコミレビュー
11. 注文履歴確認
12. 在庫少数・在庫切れ・カート内に商品を含む際のボタンの反映
13. 管理画面でのアカウント状況確認
14. 管理画面からのアカウント有効・無効切り替え
15. 管理画面からの会員情報詳細確認

## Demo
- 会員登録画面は以下。
郵便番号による住所の自動入力にajax使用しています。
登録確認後、確定を押すことで会員登録完了です。

<img width="600" alt="新規会員登録画面" src="https://user-images.githubusercontent.com/79457633/109071244-cd39f900-7736-11eb-89aa-6b3d82c0db49.png">

- ログイン後に以下のホーム画面が表示がされます。「コーヒー」と検索後、検索窓の下にヒットした件数と、検索対象が表示されています。

![list](https://user-images.githubusercontent.com/79457633/109137153-3bfd6d80-779c-11eb-8390-7ed4b8b688ae.gif)

- 以下はパスワード再設定する際に受診するメール内容です。
会員登録時に入力したメールアドレスが、退会後でなければ再設定用URLが送信されます。30分過ぎたURLは無効となります。
![mailtest](https://user-images.githubusercontent.com/79457633/109137235-4fa8d400-779c-11eb-9dbc-59feda248665.gif)

- 在庫変化による商品ページは以下。
在庫数が6より下回ると「残りわずかです」。在庫数が0になると「売り切れ」表示し、カート内に入れることを規制します。カート内に商品が含まれている場合にはアイテム数を表示させています。
![detail2](https://user-images.githubusercontent.com/79457633/109148920-36a71f80-77aa-11eb-854d-3d293747b1c0.gif)


## Usage

```
$ git clone https://github.com/mare-egg/cafeshop_portfolio.git
```

## Requirement
- Mac ver 10.15.7
- XAMPP ver 7.4.12

### 使用言語・データーベース
- PHP ver 7.3.11
- Twig
- CSS
- JS
- MySQL

### 使用ツール・ライブラリ
- jQuery
- PHPMailer
- Composer ver 2.24.3

## Author

- Takumi Onodera
- maregctm@gmail.com
- [@mare_gctm](https://twitter.com/mare_gctm)

## LICENSE

"cafeshop_portfolio" is under [MIT license](https://en.wikipedia.org/wiki/MIT_License)

## References

- [パスワードを忘れた方へをPHPで実装してみた](https://qiita.com/o-mitsuki/items/728b46a997fe8704a978 "Qiita")
- [PHPによる簡単なログイン認証いろいろ](https://qiita.com/mpyw/items/bb8305ba196f5105be15 "Qiita")
- [【PHP】Twigまとめ](https://qiita.com/assa/items/4fef2f3abd95248ed626 "Qiita")
- [初心者目線でAjaxの説明](https://qiita.com/hisamura333/items/e3ea6ae549eb09b7efb9 "Qiita")
- [MVCモデルについて](https://qiita.com/s_emoto/items/975cc38a3e0de462966a "Qiita")
- [【Git入門】Git+Github使い方入門](https://www.youtube.com/embed/LDOR5HfI_sQ "Youtube")
- PHPドキュメント
- 参考書  PHP本格入門 上
- 参考書  Webとプログラミングきほんのきほん