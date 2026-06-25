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

namespace Plugin\Recommend44\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Product;
use Plugin\Recommend44\Repository\RecommendProductRepository;

/**
 * RecommendProduct
 */
#[ORM\Table(name: 'plg_recommend_product')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: Types::STRING, length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: RecommendProductRepository::class)]
class RecommendProduct extends AbstractEntity
{
    #[ORM\Column(name: 'recommend_id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'comment', type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(name: 'sort_no', type: Types::INTEGER, nullable: true)]
    private ?int $sort_no = null;

    #[ORM\Column(name: 'visible', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $visible = true;

    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $create_date = null;

    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $update_date = null;

    #[ORM\OneToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    private ?Product $Product = null;

    /**
     * Get recommend product id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set recommend product id.
     *
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get commend.
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set comment.
     *
     * @return $this
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get rank.
     */
    public function getSortno(): ?int
    {
        return $this->sort_no;
    }

    /**
     * Set rank.
     *
     * @return $this
     */
    public function setSortno(?int $sort_no): self
    {
        $this->sort_no = $sort_no;

        return $this;
    }

    /**
     * Set visible.
     *
     * @return $this
     */
    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get del_flg.
     */
    public function getVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Set create_date.
     *
     * @return $this
     */
    public function setCreateDate(?\DateTime $createDate): self
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date.
     */
    public function getCreateDate(): ?\DateTime
    {
        return $this->create_date;
    }

    /**
     * Set update_date.
     *
     * @return $this
     */
    public function setUpdateDate(?\DateTime $updateDate): self
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date.
     */
    public function getUpdateDate(): ?\DateTime
    {
        return $this->update_date;
    }

    /**
     * Set Product.
     *
     * @return $this
     */
    public function setProduct(Product $Product): self
    {
        $this->Product = $Product;

        return $this;
    }

    /**
     * Get Product.
     */
    public function getProduct(): ?Product
    {
        return $this->Product;
    }
}
