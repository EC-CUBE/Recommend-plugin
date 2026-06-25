<?php

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

namespace Plugin\Recommend44\Tests\Repository;

use Eccube\Entity\Product;
use Eccube\Repository\ProductRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Plugin\Recommend44\Entity\RecommendProduct;
use Plugin\Recommend44\Repository\RecommendProductRepository;

/**
 * Class RecommendRepositoryTest.
 */
class RecommendRepositoryTest extends AbstractAdminWebTestCase
{
    /** @var RecommendProductRepository */
    private $recommendProductRepository;

    /** @var ProductRepository */
    private $productRepository;

    /** @var RecommendProduct */
    private $Recommend;

    /** @var RecommendProduct */
    private $Recommend2;

    /**
     * Delete all Recommend for testing.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->deleteAllRows(['plg_recommend_product']);
        $this->recommendProductRepository = $this->entityManager->getRepository(RecommendProduct::class);
        $this->productRepository = $this->entityManager->getRepository(Product::class);

        // recommend for product 1 with rank 1
        $this->Recommend = $this->initRecommendData(1, 1);
        // recommend for product 2 with rank 2
        $this->Recommend2 = $this->initRecommendData(2, 2);
    }

    /**
     * function : getMaxRank.
     */
    public function testGetMaxRank(): void
    {
        $ProductsOver = $this->recommendProductRepository->getMaxRank();

        $this->expected = 2;
        $this->actual = $ProductsOver;
        $this->verify();
    }

    /**
     * function : getRecommendProduct.
     */
    public function testGetRecommendProduct(): void
    {
        // visible=false が2件
        $RecommendProducts = $this->recommendProductRepository->getRecommendProduct();

        $this->expected = 2;
        $this->actual = count($RecommendProducts);
        $this->verify();

        // 1件をvisible=falseに変更
        /** @var RecommendProduct[] $VisibleRecommendProducts */
        $VisibleRecommendProducts = $this->recommendProductRepository->findBy(['sort_no' => 2]);
        $VisibleRecommendProducts[0]->setVisible(false);
        $this->entityManager->flush();

        // visible=false が1件
        $RecommendProducts = $this->recommendProductRepository->getRecommendProduct();

        $this->expected = 1;
        $this->actual = count($RecommendProducts);
        $this->verify();
    }

    /**
     * function : GetRecommendList.
     */
    public function testGetRecommendList(): void
    {
        $RecommendProducts = $this->recommendProductRepository->getRecommendList();

        $this->expected = 2;
        $this->actual = count($RecommendProducts);
        $this->verify();
    }

    /**
     * function : countRecommend.
     */
    public function testCountRecommend(): void
    {
        $countRecommend = $this->recommendProductRepository->countRecommend();

        $this->expected = 2;
        $this->actual = $countRecommend;
        $this->verify();
    }

    /**
     * function : moveRecommendRank.
     */
    public function testMoveRecommendRank(): void
    {
        $arrRecommend = [
            $this->Recommend->getId() => 2,
            $this->Recommend2->getId() => 1,
        ];
        $arrRankMoved = $this->recommendProductRepository->moveRecommendRank($arrRecommend);

        $this->expected = $arrRecommend[$this->Recommend->getId()];
        $this->actual = $arrRankMoved[$this->Recommend->getId()];
        $this->verify();
    }

    /**
     * function : getRecommendProductIdAll.
     */
    public function testGetRecommendProductIdAll(): void
    {
        $productIdAll = $this->recommendProductRepository->getRecommendProductIdAll();

        $this->expected = [1, 2];
        $this->actual = $productIdAll;
        $this->verify();
    }

    /**
     * function : deleteRecommend.
     */
    public function testDeleteRecommend(): void
    {
        $deleteRecommend = $this->recommendProductRepository->deleteRecommend($this->Recommend);

        $this->expected = true;
        $this->actual = $deleteRecommend;
        $this->verify();
    }

    private function initRecommendData(int $productId, int $rank): RecommendProduct
    {
        $dateTime = new \DateTime();
        $fake = $this->getFaker();

        $Recommend = new RecommendProduct();
        $Recommend->setComment($fake->word);
        $Recommend->setProduct($this->productRepository->find($productId));
        $Recommend->setSortno($rank);
        $Recommend->setVisible(true);
        $Recommend->setCreateDate($dateTime);
        $Recommend->setUpdateDate($dateTime);
        $this->entityManager->persist($Recommend);
        $this->entityManager->flush();

        return $Recommend;
    }
}
