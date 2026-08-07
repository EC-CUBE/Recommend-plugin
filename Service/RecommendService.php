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

namespace Plugin\Recommend44\Service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Plugin\Recommend44\Entity\RecommendProduct;
use Plugin\Recommend44\Repository\RecommendProductRepository;

/**
 * Class RecommendService.
 */
class RecommendService
{
    /**
     * RecommendService constructor.
     *
     * @param RecommendProductRepository $recommendProductRepository
     */
    public function __construct(private readonly RecommendProductRepository $recommendProductRepository)
    {
    }

    /**
     * おすすめ商品情報を新規登録する
     *
     * @param RecommendProduct $data
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function createRecommend(RecommendProduct $data): bool
    {
        // おすすめ商品詳細情報を生成する
        $Recommend = $this->newRecommend($data);

        return $this->recommendProductRepository->saveRecommend($Recommend);
    }

    /**
     * おすすめ商品情報を更新する
     *
     * @param RecommendProduct $data
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function updateRecommend(RecommendProduct $data): bool
    {
        // おすすめ商品情報を取得する
        $Recommend = $this->recommendProductRepository->find($data['id']);
        if (!$Recommend) {
            return false;
        }

        // おすすめ商品情報を書き換える
        $Recommend->setComment($data['comment']);
        $Recommend->setProduct($data['Product']);

        // おすすめ商品情報を更新する
        return $this->recommendProductRepository->saveRecommend($Recommend);
    }

    /**
     * おすすめ商品情報を生成する
     *
     * @param RecommendProduct $data
     *
     * @return RecommendProduct
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function newRecommend(RecommendProduct $data): RecommendProduct
    {
        $rank = $this->recommendProductRepository->getMaxRank();

        $Recommend = new RecommendProduct();
        $Recommend->setComment($data['comment']);
        $Recommend->setProduct($data['Product']);
        $Recommend->setSortno(($rank ?: 0) + 1);
        $Recommend->setVisible(true);

        return $Recommend;
    }
}
