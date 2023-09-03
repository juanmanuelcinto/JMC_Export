<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace JMC\Export\Model\Export;

use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Data\Collection as AttributeCollection;

/**
 * @api
 */
interface CategoriesCollectionFactoryInterface
{
    /**
     * @param AttributeCollection $attributeCollection
     * @param array $filters
     * @return Collection
     * @throws LocalizedException
     */
    public function create(AttributeCollection $attributeCollection, array $filters): Collection;
}
