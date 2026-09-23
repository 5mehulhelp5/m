<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Action\Preparation;

use Amasty\ExportCore\Api\ActionInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Export\Action\Preparation\Collection\Factory as CollectionPrepareFactory;
use Amasty\ExportCore\Export\Action\Preparation\Collection\PrepareCollection;
use Amasty\ExportCore\Export\Filter\Utils\RestrictedStoresFiltersApplier;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;

class PrepareCollectionAction implements ActionInterface
{
    public const DEFAULT_BATCH_SIZE = 500;

    /**
     * @var CollectionPrepareFactory
     */
    private $collectionFactory;

    /**
     * @var PrepareCollection
     */
    private $prepareCollection;

    /**
     * @var RestrictedStoresFiltersApplier|null
     */
    private $restrictedStoresFiltersApplier;

    public function __construct(
        CollectionPrepareFactory $collectionFactory,
        PrepareCollection $prepareCollection,
        ?RestrictedStoresFiltersApplier $restrictedStoresFiltersApplier = null
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->prepareCollection = $prepareCollection;
        $this->restrictedStoresFiltersApplier = $restrictedStoresFiltersApplier
            ?? ObjectManager::getInstance()->get(RestrictedStoresFiltersApplier::class);
    }

    public function initialize(ExportProcessInterface $exportProcess)
    {
        $exportProcess->setCollection($this->collectionFactory->create($exportProcess->getEntityConfig()));
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        $collection = $exportProcess->getCollection();
        $profile = $exportProcess->getProfileConfig();
        $collection->setPageSize(
            $profile->getBatchSize() ?: self::DEFAULT_BATCH_SIZE
        );
        $this->restrictedStoresFiltersApplier->setStoreIds((string)$profile->getAllowedStoreIds());
        $this->prepareCollection->execute(
            $collection,
            $profile->getEntityCode(),
            $profile->getFieldsConfig()
        );

        $exportProcess->getExportResult()->setTotalRecords($collection->getSize());
        if (!$exportProcess->getExportResult()->getTotalRecords()) {
            throw new LocalizedException(__('There are no export results'));
        }
    }
}
