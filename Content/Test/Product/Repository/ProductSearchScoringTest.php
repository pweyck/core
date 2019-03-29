<?php declare(strict_types=1);

namespace Shopware\Core\Content\Test\Product\Repository;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\EntityScoreQueryBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\SearchPattern;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\SearchTerm;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;

class ProductSearchScoringTest extends TestCase
{
    use IntegrationTestBehaviour;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityRepositoryInterface
     */
    private $repository;

    protected function setUp(): void
    {
        $this->connection = $this->getContainer()->get(Connection::class);
        $this->repository = $this->getContainer()->get('product.repository');
    }

    public function testScoringExtensionExists(): void
    {
        $pattern = new SearchPattern(new SearchTerm('test'), 'product');
        $builder = new EntityScoreQueryBuilder();
        $queries = $builder->buildScoreQueries($pattern, ProductDefinition::class, ProductDefinition::getEntityName());

        $criteria = new Criteria();
        $criteria->addQuery(...$queries);

        $context = Context::createDefaultContext();
        $this->repository->create([
            ['id' => Uuid::randomHex(), 'stock' => 10, 'name' => 'product 1 test', 'tax' => ['name' => 'test', 'taxRate' => 5], 'manufacturer' => ['name' => 'test'], 'price' => ['gross' => 10, 'net' => 9, 'linked' => false]],
            ['id' => Uuid::randomHex(), 'stock' => 10, 'name' => 'product 2 test', 'tax' => ['name' => 'test', 'taxRate' => 5], 'manufacturer' => ['name' => 'test'], 'price' => ['gross' => 10, 'net' => 9, 'linked' => false]],
        ], $context);

        $result = $this->repository->search($criteria, $context);

        /** @var Entity $entity */
        foreach ($result as $entity) {
            static::assertArrayHasKey('search', $entity->getExtensions());
            /** @var ArrayEntity $extension */
            $extension = $entity->getExtension('search');

            static::assertInstanceOf(ArrayEntity::class, $extension);
            static::assertArrayHasKey('_score', $extension);
            static::assertGreaterThan(0, (float) $extension['_score']);
        }
    }
}
