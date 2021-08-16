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

namespace Plugin\Recommend4\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Faker\Factory as Faker;
use Plugin\Recommend4\Entity\RecommendProduct;
use Plugin\Recommend4\Service\RecommendService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDummyDataCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'eccube:plugin:recommend4:fixtures:generate';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var RecommendService
     */
    protected $recommendService;

    public function __construct(
        EntityManagerInterface $entityManager,
        RecommendService $recommendService
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->recommendService = $recommendService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Dummy data generator')
            ->addOption('with-locale', null, InputOption::VALUE_REQUIRED, 'Set to the locale.', 'ja_JP')
            ->addOption('recommendproducts', null, InputOption::VALUE_REQUIRED, 'Number of Recommend Products.', 10)
            ->setHelp(<<<EOF
The <info>%command.name%</info> command generate of dummy data.

  <info>php %command.full_name%</info>
;
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locale = $input->getOption('with-locale');
        $numberOfProducts = $input->getOption('recommendproducts');

        $faker = Faker::create($locale);
        /** @var Product[] $Products */
        $Products = $this->createQueryBuilder($numberOfProducts)->getQuery()->getResult();

        foreach ($Products as $Product) {
            // @see https://github.com/fzaninotto/Faker/issues/1125#issuecomment-268676186
            gc_collect_cycles();
            switch ($output->getVerbosity()) {
                case OutputInterface::VERBOSITY_QUIET:
                    break;
                case OutputInterface::VERBOSITY_NORMAL:
                    $output->write('R');
                    break;
                case OutputInterface::VERBOSITY_VERBOSE:
                case OutputInterface::VERBOSITY_VERY_VERBOSE:
                case OutputInterface::VERBOSITY_DEBUG:
                    $output->writeln('Recommend Product: id='.$Product->getId().' '.$Product->getName().' ');
                    break;
            }

            $this->recommendService->createRecommend([
                'Product' => $Product,
                'comment' => $faker->paragraph()
            ]);
        }
    }

    /**
     * @param int|null $limit
     *
     * @return QueryBuilder
     */
    private function createQueryBuilder($limit = null)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p');

        $qb->where('p.Status in (:Status)')
            ->setParameter('Status', [ProductStatus::DISPLAY_SHOW, ProductStatus::DISPLAY_HIDE])
            ->setMaxResults($limit);

        return $qb;
    }
}
