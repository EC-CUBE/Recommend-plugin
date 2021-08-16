<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Recommend4\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;

if (!class_exists('\Plugin\Recommend4\Entity\Config', false)) {
    /**
     * Config
     *
     * @ORM\Table(name="plg_recommend_config")
     * @ORM\Entity(repositoryClass="Plugin\Recommend4\Repository\ConfigRepository")
     */
    class Config extends AbstractEntity
    {
        const OPTION_COMMENT_DEFAULT = 0;
        const OPTION_COMMENT_NOT_REQUIRED = 1;
        const OPTION_COMMENT_NOT_USE = 2;

        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(type="smallint", options={"default": 0})
         */
        private $option_use_comment = 0;

        /**
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @return integer
         */
        public function getOptionUseComment()
        {
            return $this->option_use_comment;
        }

        /**
         * @param integer $option_use_comment
         * @return Config
         */
        public function setOptionUseComment($option_use_comment)
        {
            $this->option_use_comment = $option_use_comment;
            return $this;
        }

    }
}
