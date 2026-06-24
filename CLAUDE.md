# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## このリポジトリについて

EC-CUBE 4 系の**おすすめ商品管理プラグイン**。フロントにおすすめ商品の一覧ブロックを追加し、管理画面からおすすめ商品の登録・編集・削除・並び替えを行える。

- 管理画面: 「コンテンツ管理 → おすすめ管理」（`Controller/RecommendController.php`、`Controller/RecommendSearchModelController.php`）
- フロント: おすすめ商品ブロック（`Resource/template/Block/recommend_product_block.twig`）

プラグインコードは `Recommend44`、Composer パッケージ名は `ec-cube/recommend44`。コード中の Twig 名前空間（`@Recommend44`）・クラス名前空間（`Plugin\Recommend44\...`）はすべて `Recommend44` 接頭辞を使う。

### ブランチ運用

ブランチ名が対応する EC-CUBE 本体バージョンを表す（`4.0` / `4.2` / `4.4` など）。`4.2` がデフォルトブランチ。`4.2` ブランチは EC-CUBE 4.2/4.3（`Recommend42`）に対応し、`4.4` ブランチは EC-CUBE 4.4（Symfony 7.4 / Doctrine ORM 3.0 / PHP 8.2+、`Recommend44`）に対応する。**4.3 と 4.4 はアノテーション必須/属性必須・ORM 2/3 の違いで非互換**のため、別ブランチで保守する。

## 開発・テストコマンド

このプラグイン単体では動作せず、**EC-CUBE 本体に組み込んだ状態**で開発・テストする。本体の取得・インストール・プラグイン有効化は `docker-compose.dev.yml` の entrypoint が自動実行する。

```bash
# 開発環境 (SQLite) の起動 — 本体インストール・プラグイン有効化まで自動
export COMPOSE_FILE=docker-compose.yml:docker-compose.dev.yml
docker compose up -d --wait

# MySQL / PostgreSQL で起動する場合
export COMPOSE_FILE=docker-compose.yml:docker-compose.mysql.yml:docker-compose.dev.yml
export COMPOSE_FILE=docker-compose.yml:docker-compose.pgsql.yml:docker-compose.dev.yml

# PHP バージョン切り替え（8.2-apache-4.4 / 8.3-apache-4.4 / 8.4-apache-4.4 / 8.5-apache-4.4）
TAG=8.3-apache-4.4 docker compose up -d --wait
```

起動後は管理画面 `http://localhost:8080/admin`（`admin` / `password`）、メールは MailCatcher `http://localhost:1080`。

### PHPUnit

テストは `Tests/` 配下の PHPUnit（`Tests/Repository/`・`Tests/Web/`）。`phpunit.xml.dist` により `APP_ENV=test` で実行される。

```bash
docker compose exec ec-cube bash -lc \
  "APP_ENV=test bin/console cache:clear --no-warmup && ./vendor/bin/phpunit -c app/Plugin/Recommend44/phpunit.xml.dist app/Plugin/Recommend44/Tests"
```

**注意（コンパイル済みキャッシュ）**: 有効化したプラグインのルーティングは、コンテナのコンパイル時に `dtb_plugin` を読む `EccubeExtension` で確定する。有効化直後の test キャッシュには反映されていないことがあるため、**PHPUnit 実行前に `APP_ENV=test` でキャッシュをクリアする**。これを怠るとコントローラのルートが `RouteNotFoundException` になる。

### 静的解析・整形（任意）

EC-CUBE 本体（コンテナ内）の vendor を使って実行する。

```bash
# php-cs-fixer
docker compose exec ec-cube bash -lc \
  "cd app/Plugin/Recommend44 && /var/www/html/vendor/bin/php-cs-fixer fix --config=Resource/.php-cs-fixer.dist.php --dry-run --diff"

# rector（再移行・検証用）
docker compose exec ec-cube bash -lc \
  "cd app/Plugin/Recommend44 && /var/www/html/vendor/bin/rector process --config=Resource/rector.php --dry-run"

# phpstan
docker compose exec ec-cube bash -lc \
  "cd app/Plugin/Recommend44 && /var/www/html/vendor/bin/phpstan analyse"
```

phpstan は level 6。移行前から存在する型注釈不足や phpstan-doctrine の偽陽性は `phpstan-baseline.neon` に記録して grandfather しており（`includes` で取り込み）、`analyse` は green。**新規に追加するコードは level 6 で検査される**。baseline を再生成する場合は `--generate-baseline=phpstan-baseline.neon`。

## アーキテクチャ

- **Entity** (`Entity/RecommendProduct.php`): `plg_recommend_product` テーブル。`#[ORM\*]` 属性 + 型付きプロパティ。`Product` との OneToOne、`visible` で論理削除、`sort_no` で並び順。
- **Controller** (`Controller/`): `#[Route]`/`#[Template]` 属性。一覧・新規/編集・削除・並び替え（Ajax）・商品検索モーダル。
- **Form** (`Form/Type/RecommendProductType.php`): おすすめ商品の入力フォーム。`EntityToIdTransformer` で `Product` を ID 連携。
- **Repository** (`Repository/RecommendProductRepository.php`): 一覧取得・最大 sort_no・並び替えトランザクション等。
- **Service** (`Service/RecommendService.php`): 登録/更新のドメインロジック。
- **PluginManager** (`PluginManager.php`): 有効化時におすすめ商品ブロックを配置、無効化/アンインストールで除去。全メソッド `: void`。
- **Nav** (`Nav.php`): 管理画面メニューにおすすめ管理を追加。

## 規約・移行メモ

### 開発ツール設定ファイルは `Resource/` 配下に置く（rector.php / .php-cs-fixer.dist.php）

`rector.php` や `.php-cs-fixer.dist.php` を**プラグインのルート直下に置いてはならない**。`Resource/` 配下に置く。

**理由**: EC-CUBE 本体の `config/eccube/services.yaml` がプラグインを丸ごと PSR-4 サービス検出対象として読み込む:

```yaml
Plugin\:
    resource: '../../../app/Plugin/*'
    exclude: '../../../app/Plugin/*/{Entity,Resource,ServiceProvider,Tests,Codeception,DoctrineMigrations}'
```

ルート直下の `*.php` は「サービスクラス」として読み込まれるため、`rector.php` を置くと Symfony が `Plugin\Recommend44\rector` クラスを期待し、見つからず **EC-CUBE 全体が 500 エラー**になる。`exclude` に `Resource` が含まれるため `Resource/` 配下なら衝突しない。`phpstan.neon.dist` は `.php` ではないためルートに置ける。

**将来「本体に合わせてルートへ戻す」とリグレッションするため、この配置を変更しないこと。**

### docker 環境は `APP_ENV=dev` で起動する

ブラウザログインには実セッション（`session.storage.factory.native`）が必要。`APP_ENV=test` ではモックストレージ（`mock_file`）になりログインできない。また EC-CUBE 4.4（Symfony 7）は既定 `cookie_samesite: none` のため、HTTP 環境では `dockerbuild/dev-framework.yaml`（`cookie_secure:false` / `cookie_samesite:lax`）を `app/config/eccube/packages/dev/framework.yaml` に重ねて回避している。

### プラグインの導入方法（tar + plugin:install）

`docker-compose.dev.yml` はマウントしたプラグインを `./*` で tar 化し `eccube:plugin:install --path` で導入する。`eccube:composer:require` はパッケージ API（`extra.id`）を要求するため path プラグインでは使えない。また **`PharData` は先頭の `./` エントリで展開に失敗する**ため、プラグインディレクトリ内で `./*` を対象に tar 化する（`-C dir .` は不可）。
