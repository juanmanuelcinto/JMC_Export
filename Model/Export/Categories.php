<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace JMC\Export\Model\Export;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ImportExport\Model\Export\Factory as ExportFactory;
use Magento\ImportExport\Model\Export\AbstractEntity;
use Magento\InventoryImportExport\Model\Export\ColumnProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use Magento\CatalogUrlRewrite\Model\ResourceModel\Category\GetDefaultUrlKey;

/**
 * Class Categories
 */
class Categories extends AbstractEntity
{

    /**
     * Permanent column names
     */
    const COLUMN_ENTITY_ID = 'entity_id';

    /**
     * Permanent entity columns
     *
     * @var string[]
     */
    protected $_permanentAttributes = [
        self::COLUMN_ENTITY_ID
    ];

    /**
     * Cache for category rewrite suffix
     *
     * @var null|string
     */
    protected $categoryUrlSuffix = null;

    /**
     * @var array
     */
    protected $categoryUrlKey = [];

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ExportFactory $collectionFactory
     * @param CollectionByPagesIteratorFactory $resourceColFactory
     * @param AttributeCollectionProvider $attributeCollectionProvider
     * @param CategoriesCollectionFactoryInterface $categoriesCollectionFactory
     * @param ColumnProviderInterface $columnProvider
     * @param GetDefaultUrlKey $defaultUrlKey
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ExportFactory $collectionFactory,
        CollectionByPagesIteratorFactory $resourceColFactory,
        AttributeCollectionProvider $attributeCollectionProvider,
        CategoriesCollectionFactoryInterface $categoriesCollectionFactory,
        ColumnProviderInterface $columnProvider,
        GetDefaultUrlKey $defaultUrlKey,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->defaultUrlKey = $defaultUrlKey;
        $this->attributeCollectionProvider = $attributeCollectionProvider;
        $this->categoriesCollectionFactory = $categoriesCollectionFactory;
        $this->columnProvider = $columnProvider;
        parent::__construct(
            $scopeConfig,
            $storeManager,
            $collectionFactory,
            $resourceColFactory,
            $data
        );
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getAttributeCollection()
    {
        return $this->attributeCollectionProvider->get();
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function export()
    {
        $collection = $this->categoriesCollectionFactory->create($this->getAttributeCollection(), $this->_parameters);
        $writer = $this->getWriter();
        $writer->setHeaderCols($this->_getHeaderColumns());
        foreach ($collection->getData() as $data) {
            $data['url_path'] = $this->getUrlPathWithSuffix($data['path']);
            $writer->writeRow($data);
        }

        return $writer->getContents();
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function exportItem($item)
    {
        // will not implement this method as it is legacy interface
    }

    /**
     * @inheritdoc
     */
    public function getEntityTypeCode()
    {
        return  'categories';
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _getHeaderColumns()
    {
        return array_merge(
            $this->columnProvider->getHeaders($this->getAttributeCollection(), $this->_parameters),
            ['url_path']
        );
    }

    /**
     * @inheritdoc
     */
    protected function _getEntityCollection()
    {
        // will not implement this method as it is legacy interface
    }

    /**
     * @param string $path
     * @return string
     */
    public function getUrlPathWithSuffix($path)
    {
        $pathArray = explode('/', $path);
        if (count($pathArray) <= 2) {
            return "";
        }

        $urlPathData = array_map(function ($categoryId) {
            return $this->getCategoryUrlKey((int) $categoryId);
        }, array_slice($pathArray, 2));

        return implode('/', $urlPathData) . $this->getCategoryUrlSuffix();
    }

    /**
     * @param int $categoryId
     * @return string
     */
    protected function getCategoryUrlKey($categoryId)
    {
        if (!isset($this->categoryUrlKey[$categoryId])) {
            $this->categoryUrlKey[$categoryId] = $this->defaultUrlKey->execute($categoryId);
        }

        return $this->categoryUrlKey[$categoryId];
    }

    /**
     * Retrieve category rewrite suffix for store
     *
     * @param int $storeId
     * @return string
     */
    protected function getCategoryUrlSuffix()
    {
        if ($this->categoryUrlSuffix === null) {
            $this->categoryUrlSuffix = $this->scopeConfig->getValue(
                \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX
            );
        }

        return $this->categoryUrlSuffix;
    }

}
