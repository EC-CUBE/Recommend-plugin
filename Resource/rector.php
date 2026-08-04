<?php

declare(strict_types=1);

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * https://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\ValueObject\PhpVersion;

// この設定ファイルは Rector の CLI 実行専用。
// 公開ディレクトリに配置された場合に Web 経由で実行されないようガードする。
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

return RectorConfig::configure()
    // EC-CUBE 4.4 の最小サポートバージョンに合わせる
    ->withPhpVersion(PhpVersion::PHP_82)
    // プラグインのソースディレクトリ
    ->withPaths([
        dirname(__DIR__).'/Controller',
        dirname(__DIR__).'/Entity',
        dirname(__DIR__).'/Form',
        dirname(__DIR__).'/Repository',
        dirname(__DIR__).'/Service',
        dirname(__DIR__).'/Tests',
        dirname(__DIR__).'/PluginManager.php',
        dirname(__DIR__).'/Nav.php',
    ])
    ->withSkip([
        dirname(__DIR__).'/vendor',
        dirname(__DIR__).'/node_modules',
        // テストのブートストラップは名前空間を持たないスクリプトで、import 整理により
        // use 文がライセンスヘッダより上に移動し php-cs-fixer の header_comment と
        // 衝突してヘッダが二重化するため対象外とする（phpstan も同様に除外している）
        dirname(__DIR__).'/Tests/bootstrap.php',
    ])
    ->withSets([
        LevelSetList::UP_TO_PHP_82,
        // Symfony 7.4 対応 (@Route → #[Route], @Template, buildForm(): void 等)
        SymfonySetList::SYMFONY_74,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        // Doctrine ORM 3.0 / DBAL 3.0 対応 (@ORM → #[ORM], 型付きプロパティ)
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        DoctrineSetList::DOCTRINE_DBAL_30,
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        // PHPUnit 11 対応 (静的データプロバイダ, withConsecutive() 除去等)
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_110,
    ])
    // Symfony/Doctrine 等のアノテーション → アトリビュート変換を有効化
    ->withAttributesSets()
    // #[Route] は付与されるが use 文が旧 Annotation のまま残るため Attribute へ統一する
    ->withConfiguredRule(RenameClassRector::class, [
        'Symfony\Component\Routing\Annotation\Route' => 'Symfony\Component\Routing\Attribute\Route',
    ])
    ->withImportNames(
        importShortClasses: false,
        importDocBlockNames: true,
        importNames: true
    )
    ->withParallel();
