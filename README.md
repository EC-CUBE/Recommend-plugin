# おすすめ商品管理プラグイン

[![CI for Recommend4](https://github.com/EC-CUBE/Recommend-plugin/actions/workflows/main.yml/badge.svg)](https://github.com/EC-CUBE/Recommend-plugin/actions/workflows/main.yml)

## 概要
サイトにおすすめ商品の一覧を追加することのできるプラグイン。  
[http://www.ec-cube.net/products/detail.php?product_id=1067](http://www.ec-cube.net/products/detail.php?product_id=1067)  


----------------------------------------------------------------------
## フロント画面
### F1:おすすめ商品一覧から、商品詳細ページにアクセスすることができる。
- F1-1:おすすめ商品一覧を表示する。
	- おすすめ商品ブロックを配置した場所に表示する。
	- 写真/商品名/価格/説明文を表示する。
	- 非公開商品は、おすすめ商品リストに表示されないように制御する。

- F1-2:おすすめ商品一覧の商品をクリックすると、その商品の詳細ページに移動することができる。
	- 商品詳細ページにリンクを張る。

----------------------------------------------------------------------
## 管理画面
### A1:おすすめ商品管理ページで、おすすめ商品を登録/解除することができる。
- A1-1:おすすめ商品の一覧を確認することができる。
	- メニューバーのカテゴリ管理から、おすすめ商品管理ページにアクセスできる。
	- おすすめ商品として登録されている商品の一覧を表示する。

- A1-2:おすすめ商品の一覧に、商品を登録することができる。
	- [おすすめ商品を新規登録]ボタンを押すと、おすすめ商品情報の編集ページにアクセスできる。
	- 商品の選択には、商品検索ダイアログを利用する。

- A1-3:おすすめ商品ごとに、説明文を書くことができる。
	- 商品の選択と同時に、説明文の入力ができるフォームを表示する。
	- 説明文は必ず入力しなければならない。
	- HTMLタグを利用することができる。

- A1-4:おすすめ商品情報を編集することができる。
	- おすすめ商品一覧ページに、[編集]ボタンを用意する。
	- [編集]ボタンを押すと、おすすめ商品の情報入力ページにを遷移し、情報を更新することができる。

- A1-5:おすすめ商品の一覧から、商品の登録を解除することができる。
	- おすすめ商品一覧ページに、[削除]ボタンを用意する。
	- [削除]ボタンを押すと、確認の後に、おすすめ商品の登録を削除することができる。

- A1-6:おすすめ商品の並び順を変更することができる。
	- おすすめ商品一覧ページの各行を、ドラッグ＆ドロップで移動できるようにする。
	- この並び順が、フロントに表示するときの並び順になる。

- A1-7:非公開の商品も、おすすめ商品として登録しておくことができる。
	- おすすめ商品登録時の商品検索ダイアログで、非公開の商品も検索の対象にする。

### A2:おすすめ商品一覧の表示を、ブロックとしてカスタマイズすることができる。
- A2-1:レイアウト編集画面で、おすすめ商品ブロックの表示位置を変更することができる。
	- 既存ブロックと同じように、レイアウト編集画面で[おすすめ商品ブロック]を利用することができる。
	- 全ページ適用もすることができる。
	- プラグインインストール時に、[おすすめ商品]ブロックを#main_buttomに配置しておく。

- A2-2:ブロック編集画面で、おすすめ商品ブロックの内容を変更することができる。
	- 既存ブロックと同じように、ブロック編集画面でブロック内容(twig)を編集することがでる。
	- おすすめ商品ブロックを削除することはできない。

----------------------------------------------------------------------
## Docker Compose でのテスト

Docker Compose で EC-CUBE 4.4 + 本プラグインの環境を起動し、PHPUnit を実行できます。
EC-CUBE 本体は初回起動時に自動インストールされ（デモ商品データも投入）、マウントした
プラグインが自動でインストール・有効化されます。

### 構成ファイル

| ファイル | 役割 |
|---|---|
| `docker-compose.yml` | ベース（EC-CUBE 4.4 + mailcatcher、SQLite） |
| `docker-compose.dev.yml` | プラグインのマウント・インストール・有効化 |
| `docker-compose.mysql.yml` | DB を MySQL 8 に切り替え |
| `docker-compose.pgsql.yml` | DB を PostgreSQL 18 に切り替え |

### 環境の起動

```bash
# SQLite で起動
export COMPOSE_FILE=docker-compose.yml:docker-compose.dev.yml
docker compose up -d --wait

# MySQL で起動する場合
export COMPOSE_FILE=docker-compose.yml:docker-compose.mysql.yml:docker-compose.dev.yml
docker compose up -d --wait

# PostgreSQL で起動する場合
export COMPOSE_FILE=docker-compose.yml:docker-compose.pgsql.yml:docker-compose.dev.yml
docker compose up -d --wait
```

PHP バージョンは環境変数 `TAG` で変更できます（`8.2-apache-4.4` / `8.3-apache-4.4` / `8.4-apache-4.4` / `8.5-apache-4.4`）。

```bash
TAG=8.3-apache-4.4 docker compose up -d --wait
```

### PHPUnit の実行

有効化直後はコンパイル済みキャッシュにプラグインのルーティングが反映されていない場合があるため、
テスト実行前に `cache:clear` を行います。

```bash
docker compose exec ec-cube bash -lc \
  "bin/console cache:clear --no-warmup && ./vendor/bin/phpunit -c app/Plugin/Recommend44/phpunit.xml.dist app/Plugin/Recommend44/Tests"
```

管理画面は http://localhost:8080/admin 、送信メールは http://localhost:1080 (mailcatcher) で確認できます。

### 環境の破棄

```bash
docker compose down -v
```

