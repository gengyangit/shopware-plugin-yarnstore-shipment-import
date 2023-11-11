<?php declare(strict_types=1);

namespace Yarnstore\ShipmentImport\Service\Manager;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection;

interface ShipmentQueueManagerInterface
{
    /**
     * @param array<string, mixed> $data
     * 
     * @return \Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection
     */
    public function getCollection(Criteria $criteria): ShipmentQueueCollection;

    /**
     * @param array<string, mixed> $data
     * 
     * @return \Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent
     */
    public function save(array $data): EntityWrittenContainerEvent;
}