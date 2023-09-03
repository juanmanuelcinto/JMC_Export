<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace JMC\Export\Model\Export;

use Magento\Framework\Data\Collection as AttributeCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use JMC\Export\Model\Export\ColumnProviderInterface;
use Magento\ImportExport\Model\Export;
use JMC\Export\Model\Export\FilterProcessorAggregator;

/**
 * @inheritdoc
 */
class CategoriesCollectionFactory implements CategoriesCollectionFactoryInterface
{

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ColumnProviderInterface
     */
    private $columnProvider;

    /**
     * @var FilterProcessorAggregator
     */
    private $filterProcessor;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ColumnProviderInterface $columnProvider
     * @param FilterProcessorAggregator $filterProcessor
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ColumnProviderInterface $columnProvider,
        FilterProcessorAggregator $filterProcessor
    ) {
        $this->objectManager = $objectManager;
        $this->columnProvider = $columnProvider;
        $this->filterProcessor = $filterProcessor;
    }

    /**
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return Collection
     * @throws LocalizedException
     */
    public function create(AttributeCollection $attributeCollection, array $filters): Collection
    {
        /** @var Collection $collection */
        $collection = $this->objectManager->create(Collection::class);
        $columns = $this->columnProvider->getColumns($attributeCollection, $filters);
        $collection->addFieldToSelect($columns);

        foreach ($this->retrieveFilterData($filters) as $columnName => $value) {
            $attributeDefinition = $attributeCollection->getItemById($columnName);
            if (!$attributeDefinition) {
                throw new LocalizedException(__(
                    'Given column name "%columnName" is not present in collection.',
                    ['columnName' => $columnName]
                ));
            }

            $type = $attributeDefinition->getData('backend_type');
            if (!$type) {
                throw new LocalizedException(__(
                    'There is no backend type specified for column "%columnName".',
                    ['columnName' => $columnName]
                ));
            }

            $this->filterProcessor->process($type, $collection, $columnName, $value);
        }

        return $collection;
    }

    /**
     * @param array $filters
     * @return array
     */
    private function retrieveFilterData(array $filters)
    {
        return array_filter(
            $filters[Export::FILTER_ELEMENT_GROUP] ?? [],
            function ($value) {
                return $value !== '';
            }
        );
    }
}
