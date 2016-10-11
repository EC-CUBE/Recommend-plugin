<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version201510211300
 * @package DoctrineMigrations
 */
class Version201510211300 extends AbstractMigration
{
    /**
     * @var string table name
     */
    const NAME = 'plg_recommend_product';
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->createRecommendProduct($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable(self::NAME);
        $schema->dropSequence('plg_recommend_product_recommend_product_id_seq');
    }

    /**
     * @param Schema $schema
     * @return bool
     */
    protected function createRecommendProduct(Schema $schema)
    {
        if ($schema->hasTable(self::NAME)) {
            return true;
        }

        $app = Application::getInstance();
        $em = $app['orm.em'];
        $classes = array(
            $em->getClassMetadata('Plugin\Recommend\Entity\RecommendProduct'),
        );
        $tool = new SchemaTool($em);
        $tool->createSchema($classes);

        return true;
    }
}
