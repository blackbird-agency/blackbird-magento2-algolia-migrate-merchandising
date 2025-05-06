<?php
/**
 * MigrateMerchandising
 *
 * @copyright Copyright © 2025 Blackbird. All rights reserved.
 * @author    emilie (Blackbird Team)
 */
declare(strict_types=1);


namespace Blackbird\AlgoliaMigrateMerchandising\Model\Service;

use Algolia\AlgoliaSearch\DataProvider\Analytics\IndexEntityDataProvider;
use Algolia\AlgoliaSearch\Helper\AlgoliaHelper;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Algolia\AlgoliaSearch\Api\SearchClient;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Blackbird\AlgoliaMigrateMerchandising\Api\Data\MigrateMerchandisingServiceInterface;
use Algolia\AlgoliaSearch\Helper\Entity\CategoryHelper;

class MigrateMerchandising implements MigrateMerchandisingServiceInterface
{
    /**
     * @var SearchClient
     */
    protected $client;

    protected int $storeId;

    public function __construct(
        protected readonly ConfigHelper $configHelper,
        protected AlgoliaHelper $algoliaHelper,
        protected readonly StoreManagerInterface $storeManager,
        protected readonly CategoryRepositoryInterface $categoryRepository,
        protected readonly CategoryHelper $categoryHelper,
        protected readonly IndexEntityDataProvider $entityHelper
    ) {
    }

    public function migrate(string $storeCode, string $file): void
    {
        try {
            $this->setStoreId($storeCode);
            $indexName = $this->entityHelper->getIndexNameByEntity('products', $this->getStoreId());
            $initialRules = $this->getRules($file);
            $finalRules = $this->formatRules($initialRules);
            $this->saveRules($finalRules, $indexName);
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \RuntimeException('An unexpected error occurred during migration.');
        }
    }

    protected function setStoreId(string $storeCode): void
    {
        $this->storeId = (int)$this->storeManager->getStore($storeCode)->getId();
    }

    protected function getStoreId(): int
    {
        return $this->storeId;
    }

    protected function getRules(string $file): array
    {
        if (!\file_exists($file)) {
            throw new \RuntimeException($file . ' does not exist');
        }

        $jsonData = \file_get_contents($file);
        $dataArray = \json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);

        if ($dataArray === null) {
            throw new \RuntimeException('Could not read json data');
        }

        return $dataArray;
    }

    protected function formatRules(array $rules): array
    {
        foreach ($rules as $key => $rule) {
            if (!isset($rule['objectID'])) {
                unset($rules[$key]);
                continue;
            }
            if (\preg_match('/magento-category-(\d+)/', $rule['objectID'], $matches)) {
                $categoryId = (int)$matches[1];
                $categoryPath = $this->getCategoryPath($categoryId);

                if (!$categoryPath) {
                    unset($rules[$key]);
                    continue;
                }
                unset($rules[$key]['conditions'][0]['context']);
                $rules[$key]['conditions'][0]['filters'] = '"categoryPageId":"' . $categoryPath . '"';
                $rules[$key]['objectID'] = 'qr-' . $categoryId . '-migrate';
                $rules[$key]['tags'] = ['visual-editor'];
            } else {
                unset($rules[$key]);
            }
        }

        return $rules;
    }

    protected function getCategoryPath(int $categoryId): ?string
    {
        $path = '';
        try {
            $category = $this->categoryRepository->get($categoryId);
            foreach ($category->getPathIds() as $treeCategoryId) {
                if ($path !== '') {
                    $path .= ' /// ';
                }

                $path .= $this->categoryHelper->getCategoryName($treeCategoryId, $this->getStoreId());
            }
        } catch (\Exception) {
            return null;
        }
        return $path;
    }

    protected function saveRules(array $rules, string $indexName): void
    {
        $client = $this->getClient();
        $client->saveRules($indexName, $rules);
    }

    protected function getClient(): SearchClient
    {
        if (!$this->client) {
            $this->client = SearchClient::create(
                $this->configHelper->getApplicationID(),
                $this->configHelper->getAPIKey()
            );
        }

        return $this->client;

    }
}
