<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace JMC\Export\Model\Export;

use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Data\Collection;
use Magento\ImportExport\Model\Export\Factory as CollectionFactory;
use JMC\Export\Model\Export\Source\YesNo;
use Magento\Eav\Model\Entity\Attribute\Frontend\Datetime;

/**
 * @api
 */
class AttributeCollectionProvider
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param AttributeFactory $attributeFactory
     * @throws \InvalidArgumentException
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        AttributeFactory $attributeFactory
    ) {
        $this->collection = $collectionFactory->create(Collection::class);
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function get(): Collection
    {
        if (count($this->collection) === 0) {
            /** @var \Magento\Eav\Model\Entity\Attribute $entityIdAttribute */
            $entityIdAttribute = $this->attributeFactory->create();
            $entityIdAttribute->setId('entity_id');
            $entityIdAttribute->setDefaultFrontendLabel(__('Category Id'));
            $entityIdAttribute->setAttributeCode('entity_id');
            $entityIdAttribute->setBackendType('varchar');
            $this->collection->addItem($entityIdAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $attributeSetIdAttribute */
            $attributeSetIdAttribute = $this->attributeFactory->create();
            $attributeSetIdAttribute->setId('attribute_set_id');
            $attributeSetIdAttribute->setDefaultFrontendLabel(__('Attribute Set Id'));
            $attributeSetIdAttribute->setAttributeCode('attribute_set_id');
            $attributeSetIdAttribute->setBackendType('varchar');
            $this->collection->addItem($attributeSetIdAttribute);

            // /** @var \Magento\Eav\Model\Entity\Attribute $parentIdAttribute */
            $parentIdAttribute = $this->attributeFactory->create();
            $parentIdAttribute->setId('parent_id');
            $parentIdAttribute->setDefaultFrontendLabel(__('Parent Id'));
            $parentIdAttribute->setAttributeCode('parent_id');
            $parentIdAttribute->setBackendType('varchar');
            $this->collection->addItem($parentIdAttribute);
            //
            // /** @var \Magento\Eav\Model\Entity\Attribute $createdAtAttribute */
            $createdAtAttribute = $this->attributeFactory->create();
            $createdAtAttribute->setId('created_at');
            $createdAtAttribute->setDefaultFrontendLabel(__('Created At'));
            $createdAtAttribute->setAttributeCode('created_at');
            $createdAtAttribute->setFrontendClass(Datetime::class);
            $createdAtAttribute->setBackendType('datetime');
            $this->collection->addItem($createdAtAttribute);

            // /** @var \Magento\Eav\Model\Entity\Attribute $createdInAttribute */
            $updatedAtAttribute = $this->attributeFactory->create();
            $updatedAtAttribute->setId('updated_at');
            $updatedAtAttribute->setDefaultFrontendLabel(__('Created At'));
            $updatedAtAttribute->setAttributeCode('updated_at');
            $updatedAtAttribute->setFrontendClass(Datetime::class);
            $updatedAtAttribute->setBackendType('datetime');
            $this->collection->addItem($updatedAtAttribute);

            // /** @var \Magento\Eav\Model\Entity\Attribute $pathAttribute */
            $pathAttribute = $this->attributeFactory->create();
            $pathAttribute->setId('path');
            $pathAttribute->setDefaultFrontendLabel(__('Path'));
            $pathAttribute->setAttributeCode('path');
            $pathAttribute->setBackendType('varchar');
            $this->collection->addItem($pathAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $positionAttribute */
            $positionAttribute = $this->attributeFactory->create();
            $positionAttribute->setId('position');
            $positionAttribute->setDefaultFrontendLabel(__('Position'));
            $positionAttribute->setAttributeCode('position');
            $positionAttribute->setBackendType('int');
            $this->collection->addItem($positionAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $levelAttribute */
            $levelAttribute = $this->attributeFactory->create();
            $levelAttribute->setId('level');
            $levelAttribute->setDefaultFrontendLabel(__('Level'));
            $levelAttribute->setAttributeCode('level');
            $levelAttribute->setBackendType('int');
            $this->collection->addItem($levelAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $childrenCountAttribute */
            $childrenCountAttribute = $this->attributeFactory->create();
            $childrenCountAttribute->setId('children_count');
            $childrenCountAttribute->setDefaultFrontendLabel(__('Chldren Count'));
            $childrenCountAttribute->setAttributeCode('children_count');
            $childrenCountAttribute->setBackendType('int');
            $this->collection->addItem($childrenCountAttribute);
        }
        return $this->collection;
    }
}
